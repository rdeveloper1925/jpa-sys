<?php
namespace App\Controllers;
use Config\Database;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use org\bovigo\vfs\vfsStreamContainerIterator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Color;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Style\FillPattern;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Finance extends BaseController{
    public function index(){
        $db=Database::connect();
        $customers=$db->table('customers')->get()->getResultObject();
        $finances=$db->table('finance')->
        select('*')->orderBy('id', 'DESC')->get()->getResult('object');
        return view('finance/index', ['title'=>'Finances', 'finances'=>$finances,'customers'=>$customers]);
    }

    public function save(){
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/Finance.log', Logger::INFO));
        $db=Database::connect();
        //checking if invoice already exists
        $taxInvoiceNo=$this->request->getVar('taxInvoiceNo');
        $proformaNo=$this->request->getVar('proformaNo');
        $rs=$db->query("SELECT * FROM finance WHERE proformaNo=$proformaNo OR taxInvoiceNo=$taxInvoiceNo")->getResultArray();
        if(!empty($rs)){
            session()->setFlashdata('fail',"The supplied tax Invoice number or proforma number already exists");
            return redirect()->to(base_url('finance'));
        }
        //confirming a transaction if it is already cleared
        if($this->request->getVar('cleared')){
            $confirmed=1;
        }else{
            $confirmed=$this->request->getVar('confirmed');
        }
        $entry=array(
            'date'=>$this->request->getVar('date'),
            'proformaNo'=>$this->request->getVar('proformaNo'),
            'taxInvoiceNo'=>$this->request->getVar('taxInvoiceNo'),
            'lpoNo'=>$this->request->getVar('lpoNo'),
            'customerId'=>$this->request->getVar('customerId'),
            'customerName'=>$this->request->getVar('customerName'),
            'confirmed'=>$confirmed,
            'withholdingTax'=>$this->request->getVar('withholdingTax'),
            'vat'=>$this->request->getVar('vat'),
            'totalPayable'=>$this->request->getVar('totalPayable'),
            'cleared'=>$this->request->getVar('cleared'),
            'email'=>$this->request->getVar('email'),
            'phone'=>$this->request->getVar('phone'),
            'areaCountry'=>$this->request->getVar('areaCountry'),
            'address'=>$this->request->getVar('address'),
            'tinNo'=>$this->request->getVar('tinNo'),
            'carRegNo'=>$this->request->getVar('carRegNo'),
            'contactPerson'=>$this->request->getVar('contactPerson')
        );
        $db->table('finance')->insert($entry);
        $logger->info("New financial entry made",['maker'=>session()->get('fullName')]);
        session()->setFlashdata('success','New financial Entry has been entered');
        return redirect()->to(base_url('finance'));
    }

    public function fetchEntry(){
        $db=Database::connect();
        $id=$this->request->getVar('id');
        $entry=$db->table('finance')->getWhere(['id'=>$id])->getResultObject()[0];
        $data['success']=1;
        $data['entry']=$entry;
        return json_encode($data);
    }

    public function update(){
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/Finance.log', Logger::INFO));
        $entryId=$this->request->getVar('entryId');
        $db=Database::connect();
        //confirming a transaction if it is already cleared
        if($this->request->getVar('cleared')){
            $confirmed=1;
        }else{
            $confirmed=$this->request->getVar('confirmed');
        }
        $entry=array(
           // 'date'=>$this->request->getVar('date'),
            'proformaNo'=>$this->request->getVar('proformaNo'),
            'taxInvoiceNo'=>$this->request->getVar('taxInvoiceNo'),
            'lpoNo'=>$this->request->getVar('lpoNo'),
            'customerId'=>$this->request->getVar('customerId'),
            'customerName'=>$this->request->getVar('customerName'),
            'confirmed'=>$confirmed,
            'withholdingTax'=>$this->request->getVar('withholdingTax'),
            'vat'=>$this->request->getVar('vat'),
            'totalPayable'=>$this->request->getVar('totalPayable'),
            'cleared'=>$this->request->getVar('cleared'),
            'carRegNo'=>$this->request->getVar('carRegNo'),
            'email'=>$this->request->getVar('email'),
            'phone'=>$this->request->getVar('phone'),
            'areaCountry'=>$this->request->getVar('areaCountry'),
            'address'=>$this->request->getVar('address'),
            'tinNo'=>$this->request->getVar('tinNo'),
            'contactPerson'=>$this->request->getVar('contactPerson')
        );
        $db->table('finance')->update($entry,['id'=>$entryId]);
        $logger->info("Finance entry edited successfully by ",['maker'=>session()->get('fullName')]);
        session()->setFlashdata('success','Entry edited successfully');
        return redirect()->to(base_url('finance'));
    }

    public function delete($id=0){
        $db=Database::connect();
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/Finance.log', Logger::INFO));
        $db->table('finance')->delete(['id'=>$id]);
        $logger->info("Financial entry with id=$id has been deleted",['maker'=>session()->get('fullName')]);
        return json_encode(['success'=>1,'message'=>'Financial entry deleted successfully']);
    }

    public function report_select(){
        $db=Database::connect();
        $customers=$db->query("select distinct(a.id),a.customerName from customers a, finance b where a.id=b.customerId")->getResultObject();
        return view('finance/report_select',['title'=>'Select Report','customers'=>$customers]);
    }

    public function view_report(){
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/Finance.log', Logger::INFO));
        $customer=$this->request->getVar('customer');
        $from=$this->request->getVar('from');
        $to=$this->request->getVar('to');
        //check dates to prevent errors
        if($to<$from){
            session()->setFlashdata('fail','The to date has to be latest than from date');
            return redirect()->to(base_url('finance/report_select'));
        }
        //if all customers are required
        if($customer == '*'){
            $logger->info("Generating all customers report by",['maker'=>session()->get('fullName')]);
            return redirect()->to(base_url("finance/allCustomersAllReports/$from/$to"));
        }else{
            $logger->info("Generating specific customer ($customer) report by",['User'=>session()->get('fullName')]);
            return redirect()->to(base_url("finance/specificCustomerAllReports/$from/$to/$customer"));
        }


        //return json_encode($this->request->getPost());
        return view('visuals/index',['title'=>'visualizations']);
    }

    public function allCustomersAllReports($from,$to){
        //check dates to prevent errors
        if($to<$from){
            session()->setFlashdata('fail','The to date has to be latest than from date');
            return redirect()->to(base_url('finance/report_select'));
        }
        $db=Database::connect();
        $confirmed=$db->query("SELECT COUNT(*) AS VOLUME,SUM(totalPayable) AS VALUE FROM finance WHERE CONFIRMED=1 and date between '$from' and '$to'")->getResultObject()[0];
        $unconfirmed=$db->query("SELECT COUNT(*) AS VOLUME,SUM(totalPayable) AS VALUE FROM finance WHERE CONFIRMED=0 and date between '$from' and '$to'")->getResultObject()[0];
        $cleared=$db->query("SELECT COUNT(*) AS VOLUME, SUM(totalPayable) AS VALUE FROM finance WHERE CLEARED=1 and confirmed=1 and date between '$from' and '$to'")->getResultObject()[0];
        $uncleared=$db->query("SELECT COUNT(*) AS VOLUME, SUM(totalPayable) AS VALUE FROM finance WHERE CLEARED=0 and confirmed=1 and date between '$from' and '$to'")->getResultObject()[0];
        $confirmedAndCleared=$db->query("SELECT COUNT(*) AS VOLUME, SUM(totalPayable) AS VALUE FROM finance WHERE CLEARED=1 AND confirmed=1 and date between '$from' and '$to'")->getResultObject()[0];
        $unconfirmedAndUnCleared=$db->query("SELECT COUNT(*) AS VOLUME, SUM(totalPayable) AS VALUE FROM finance WHERE CLEARED=0 AND confirmed=0 and date between '$from' and '$to'")->getResultObject()[0];

        $data['title']="Information for All Customers and All Reports from ".date('Y-M-d',strtotime($from))." to ".date('Y-M-d',strtotime($to));
        $data['entries']=$db->query("SELECT COUNT(*) AS COUNT FROM FINANCE WHERE date between '$from' and '$to'")->getResultObject()[0]->COUNT;
        $data['entryValue']=$db->query("SELECT SUM(totalPayable) as VALUE FROM FINANCE where date between '$from' and '$to'")->getResultObject()[0]->VALUE;
        $data['confirmedVolume']=$confirmed->VOLUME;
        $data['confirmedValue']=$confirmed->VALUE;
        $data['unconfirmedVolume']=$unconfirmed->VOLUME;
        $data['unconfirmedValue']=$unconfirmed->VALUE;
        $data['clearedVolume']=$cleared->VOLUME;
        $data['clearedValue']=$cleared->VALUE;
        $data['unclearedVolume']=$uncleared->VOLUME;
        $data['unclearedValue']=$uncleared->VALUE;
        $data['confirmedAndClearedVolume']=$confirmedAndCleared->VOLUME;
        $data['confirmedAndClearedValue']=$confirmedAndCleared->VALUE;
        $data['confirmedAndUnClearedVolume']=$data['entries']-$confirmedAndCleared->VOLUME;
        $data['confirmedAndUnClearedValue']=$data['entryValue']-$confirmedAndCleared->VALUE;
        $data['unconfirmedAndUnClearedVolume']=$unconfirmedAndUnCleared->VOLUME;
        $data['unconfirmedAndUnClearedValue']=$unconfirmedAndUnCleared->VALUE;
        $data['trend']=$db->query("select count(*) as VOLUME,sum(totalPayable) AS VALUE, year(date) AS YEAR, date_format(date,'%b') AS MONTH from finance where cleared=1 and date between '$from' and '$to' group by year(date),month(date) order by year(date) desc LIMIT 12")->getResultObject();
        $data['trendMaxValue']=$db->query("select max(VALUE) AS MAX_VALUE FROM (SELECT sum(totalPayable) AS VALUE FROM FINANCE finance where cleared=1 and date between '$from' and '$to' group by year(date),month(date) ORDER BY YEAR(DATE) DESC LIMIT 12) AS FIN2;")->getResultObject()[0]->MAX_VALUE+400000;
        $data['customerId']='*';
        $data['from']=$from;
        $data['to']=$to;
        return view('visuals/index',$data);
    }

    public function specificCustomerAllReports($from,$to,$customer){
        //check dates to prevent errors
        if($to<$from || is_null($customer)){
            session()->setFlashdata('fail','The to date has to be latest than from date');
            return redirect()->to(base_url('finance/report_select'));
        }
        $db=Database::connect();
        //getting the customer name
        $customerName=$db->table('customers')->getWhere(['id'=>$customer])->getResultObject()[0]->customerName;
        if(is_null($customerName)){
            session()->setFlashdata('fail','Customer Name not found');
            return redirect()->to(base_url('finance/report_select'));
        }
        $confirmed=$db->query("SELECT COUNT(*) AS VOLUME,SUM(totalPayable) AS VALUE FROM finance WHERE CONFIRMED=1 and customerId='$customer' and date between '$from' and '$to'")->getResultObject()[0];
        $unconfirmed=$db->query("SELECT COUNT(*) AS VOLUME,SUM(totalPayable) AS VALUE FROM finance WHERE CONFIRMED=0 and customerId='$customer' and date between '$from' and '$to'")->getResultObject()[0];
        $cleared=$db->query("SELECT COUNT(*) AS VOLUME, SUM(totalPayable) AS VALUE FROM finance WHERE CLEARED=1 and confirmed=1 and customerId='$customer' and date between '$from' and '$to'")->getResultObject()[0];
        $uncleared=$db->query("SELECT COUNT(*) AS VOLUME, SUM(totalPayable) AS VALUE FROM finance WHERE CLEARED=0 and confirmed=1 and customerId='$customer' and date between '$from' and '$to'")->getResultObject()[0];
        $confirmedAndCleared=$db->query("SELECT COUNT(*) AS VOLUME, SUM(totalPayable) AS VALUE FROM finance WHERE CLEARED=1 AND confirmed=1 and customerId='$customer' and date between '$from' and '$to'")->getResultObject()[0];
        $unconfirmedAndUnCleared=$db->query("SELECT COUNT(*) AS VOLUME, SUM(totalPayable) AS VALUE FROM finance WHERE CLEARED=0 AND confirmed=0 and customerId='$customer' and date between '$from' and '$to'")->getResultObject()[0];

        $data['title']="Information for $customerName and All Reports from ".date('Y-M-d',strtotime($from))." to ".date('Y-M-d',strtotime($to));
        $data['entries']=$db->query("SELECT COUNT(*) AS COUNT FROM FINANCE WHERE customerId='$customer' and date between '$from' and '$to'")->getResultObject()[0]->COUNT;
        $data['entryValue']=$db->query("SELECT SUM(totalPayable) as VALUE FROM FINANCE where customerId='$customer' and date between '$from' and '$to'")->getResultObject()[0]->VALUE;
        $data['confirmedVolume']=$confirmed->VOLUME;
        $data['confirmedValue']=$confirmed->VALUE;
        $data['unconfirmedVolume']=$unconfirmed->VOLUME;
        $data['unconfirmedValue']=$unconfirmed->VALUE;
        $data['clearedVolume']=$cleared->VOLUME;
        $data['clearedValue']=$cleared->VALUE;
        $data['unclearedVolume']=$uncleared->VOLUME;
        $data['unclearedValue']=$uncleared->VALUE;
        $data['confirmedAndClearedVolume']=$confirmedAndCleared->VOLUME;
        $data['confirmedAndClearedValue']=$confirmedAndCleared->VALUE;
        $data['confirmedAndUnClearedVolume']=$data['entries']-$confirmedAndCleared->VOLUME;
        $data['confirmedAndUnClearedValue']=$data['entryValue']-$confirmedAndCleared->VALUE;
        $data['unconfirmedAndUnClearedVolume']=$unconfirmedAndUnCleared->VOLUME;
        $data['unconfirmedAndUnClearedValue']=$unconfirmedAndUnCleared->VALUE;
        $data['trend']=$db->query("select count(*) as VOLUME,sum(totalPayable) AS VALUE, year(date) AS YEAR, date_format(date,'%b') AS MONTH from finance where cleared=1 and customerId='$customer' and date between '$from' and '$to' group by year(date),month(date) order by year(date) desc LIMIT 12")->getResultObject();
        $data['trendMaxValue']=$db->query("select max(VALUE) AS MAX_VALUE FROM (SELECT sum(totalPayable) AS VALUE FROM FINANCE finance where cleared=1 and customerId='$customer' and date between '$from' and '$to' group by year(date),month(date) ORDER BY YEAR(DATE) DESC LIMIT 12) AS FIN2;")->getResultObject()[0]->MAX_VALUE+400000;
        $data['customerId']=$customer;
        $data['customerName']=$customerName;
        $data['from']=$from;
        $data['to']=$to;
        return view('visuals/index_single_customer',$data);
    }

    public function generate(){
        $db=Database::connect();
        $from=$this->request->getVar('from');
        $to=$this->request->getVar('to');
        $id=$this->request->getVar('customerId');
        //check dates to prevent errors
        if($to<$from){
            session()->setFlashdata('fail','The to date has to be latest than from date');
            return redirect()->to(base_url('finance/report_select'));
        }
        $paid=$db->query("SELECT * FROM FINANCE WHERE CONFIRMED=1 AND CLEARED=1 AND DATE BETWEEN '$from' and '$to'")->getResultObject();
        $unpaid=$db->query("SELECT * FROM FINANCE WHERE CONFIRMED=1 AND CLEARED=0 AND DATE BETWEEN '$from' and '$to'")->getResultObject();
        $unconfirmed=$db->query("SELECT * FROM FINANCE WHERE CONFIRMED=0 and DATE BETWEEN '$from' and '$to'")->getResultObject();
        $paidSummary=$db->query("SELECT COUNT(*) AS VOLUME,SUM(totalPayable) AS VALUE FROM finance WHERE CONFIRMED=1 AND CLEARED=1 AND DATE BETWEEN '$from' and '$to'")->getResultObject()[0];
        $unpaidSummary=$db->query("SELECT COUNT(*) AS VOLUME,SUM(totalPayable) AS VALUE FROM finance WHERE CONFIRMED=1 AND CLEARED=0 AND DATE BETWEEN '$from' and '$to'")->getResultObject()[0];
        $unconfirmedSummary=$db->query("SELECT COUNT(*) AS VOLUME,SUM(totalPayable) AS VALUE FROM finance WHERE CONFIRMED=0 AND DATE BETWEEN '$from' and '$to'")->getResultObject()[0];
        //paid
        try {
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet=$reader->load('app/Views/reportTemplates/Financial_Report_template.xlsx');
            $spreadsheet->setActiveSheetIndex(0); //0=>paid
            $sheet=$spreadsheet->getActiveSheet();
            $sheet->setCellValue('L2',date('Y-M-d',strtotime($from)).' - '.date('Y-M-d',strtotime($to)))
                ->setCellValue('L3',$paidSummary->VOLUME)
                ->setCellValue('L4',$paidSummary->VALUE);
            $colored=array(
                'font'=>array(
                    'bold'=>true
                ),
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFDDC2',
                    ],
                )
            );
            $colored_total=array(
                'font'=>array(
                    'bold'=>true
                ),
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => '60FF7E',
                    ],
                )
            );
            $color=true;
            $currentRow=8;$start=8;
            foreach ($paid as $p){
                //insert new row
                $sheet->insertNewRowBefore($currentRow+1,1);
                if($color){
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored);
                    $color=false;
                }else{
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->getFont()->setBold(true);
                    $color=true;
                }
                $sheet->setCellValue('B'.$currentRow,$p->id)
                    ->setCellValue('C'.$currentRow,$p->customerName)
                    ->setCellValue('d'.$currentRow,$p->proformaNo)
                    ->setCellValue('e'.$currentRow,$p->taxInvoiceNo)
                    ->setCellValue('f'.$currentRow,$p->lpoNo)
                    ->setCellValue('g'.$currentRow,$p->withholdingTax)
                    ->setCellValue('h'.$currentRow,$p->vat)
                    ->setCellValue('i'.$currentRow,$p->totalPayable)
                    ->setCellValue('j'.$currentRow,"CLEARED")
                    ->setCellValue('k'.$currentRow,$p->contactPerson)
                    ->setCellValue('l'.$currentRow,$p->carRegNo)
                    ->setCellValue('m'.$currentRow,$p->phone)
                    ->setCellValue('N'.$currentRow,date('Y-M-d',strtotime($p->date)));
                $currentRow++;
            }
            //totals row
            $sheet->insertNewRowBefore($currentRow+1,1);
            $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored_total)->getFont()->setSize(12);
            $sheet->mergeCells('b'.$currentRow.':c'.$currentRow);
            $sheet->setCellValue('b'.$currentRow,'TOTALS CLEARED');
            $sheet->setCellValue('g'.$currentRow,"=SUM(G$start:G$currentRow)");
            $sheet->setCellValue('h'.$currentRow,"=SUM(H$start:H$currentRow)");
            $sheet->setCellValue('I'.$currentRow,"=SUM(I$start:I$currentRow)");
            //-----------------------------------------end of paid


            //-----------------------------------------start of unpaid
            $spreadsheet->setActiveSheetIndex(1); //1=>unpaid
            $sheet=$spreadsheet->getActiveSheet();
            $sheet->setCellValue('L2',date('Y-M-d',strtotime($from)).' - '.date('Y-M-d',strtotime($to)))
                ->setCellValue('L3',$unpaidSummary->VOLUME)
                ->setCellValue('L4',$unpaidSummary->VALUE);
            $color=true;
            $currentRow=8;$start=8;
            foreach ($unpaid as $p){
                //insert new row
                $sheet->insertNewRowBefore($currentRow+1,1);
                if($color){
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored);
                    $color=false;
                }else{
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->getFont()->setBold(true);
                    $color=true;
                }
                $sheet->setCellValue('B'.$currentRow,$p->id)
                    ->setCellValue('C'.$currentRow,$p->customerName)
                    ->setCellValue('d'.$currentRow,$p->proformaNo)
                    ->setCellValue('e'.$currentRow,$p->taxInvoiceNo)
                    ->setCellValue('f'.$currentRow,$p->lpoNo)
                    ->setCellValue('g'.$currentRow,$p->withholdingTax)
                    ->setCellValue('h'.$currentRow,$p->vat)
                    ->setCellValue('i'.$currentRow,$p->totalPayable)
                    ->setCellValue('j'.$currentRow,"UNCLEARED")
                    ->setCellValue('k'.$currentRow,$p->contactPerson)
                    ->setCellValue('l'.$currentRow,$p->carRegNo)
                    ->setCellValue('m'.$currentRow,$p->phone)
                    ->setCellValue('N'.$currentRow,date('Y-M-d',strtotime($p->date)));
                $currentRow++;
            }
            //totals row
            $sheet->insertNewRowBefore($currentRow+1,1);
            $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored_total)->getFont()->setSize(12);
            $sheet->mergeCells('b'.$currentRow.':c'.$currentRow);
            $sheet->setCellValue('b'.$currentRow,'TOTALS UNCLEARED');
            $sheet->setCellValue('g'.$currentRow,"=SUM(G$start:G$currentRow)");
            $sheet->setCellValue('h'.$currentRow,"=SUM(H$start:H$currentRow)");
            $sheet->setCellValue('I'.$currentRow,"=SUM(I$start:I$currentRow)");

            //-----------------------------------------start of unconfirmed
            $spreadsheet->setActiveSheetIndex(2); //2=>unconfirmed
            $sheet=$spreadsheet->getActiveSheet();
            $sheet->setCellValue('L2',date('Y-M-d',strtotime($from)).' - '.date('Y-M-d',strtotime($to)))
                ->setCellValue('L3',$unconfirmedSummary->VOLUME)
                ->setCellValue('L4',$unconfirmedSummary->VALUE);
            $color=true;
            $currentRow=8;$start=8;
            foreach ($unconfirmed as $p){
                //insert new row
                $sheet->insertNewRowBefore($currentRow+1,1);
                if($color){
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored);
                    $color=false;
                }else{
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->getFont()->setBold(true);
                    $color=true;
                }
                $sheet->setCellValue('B'.$currentRow,$p->id)
                    ->setCellValue('C'.$currentRow,$p->customerName)
                    ->setCellValue('d'.$currentRow,$p->proformaNo)
                    ->setCellValue('e'.$currentRow,$p->taxInvoiceNo)
                    ->setCellValue('f'.$currentRow,$p->lpoNo)
                    ->setCellValue('g'.$currentRow,$p->withholdingTax)
                    ->setCellValue('h'.$currentRow,$p->vat)
                    ->setCellValue('i'.$currentRow,$p->totalPayable)
                    ->setCellValue('j'.$currentRow,"UNCONFIRMED")
                    ->setCellValue('k'.$currentRow,$p->contactPerson)
                    ->setCellValue('l'.$currentRow,$p->carRegNo)
                    ->setCellValue('m'.$currentRow,$p->phone)
                    ->setCellValue('N'.$currentRow,date('Y-M-d',strtotime($p->date)));
                $currentRow++;
            }
            //totals row
            $sheet->insertNewRowBefore($currentRow+1,1);
            $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored_total)->getFont()->setSize(12);
            $sheet->mergeCells('b'.$currentRow.':c'.$currentRow);
            $sheet->setCellValue('b'.$currentRow,'TOTALS UNCONFIRMED');
            $sheet->setCellValue('g'.$currentRow,"=SUM(G$start:G$currentRow)");
            $sheet->setCellValue('h'.$currentRow,"=SUM(H$start:H$currentRow)");
            $sheet->setCellValue('I'.$currentRow,"=SUM(I$start:I$currentRow)");


            //producing the excel
            $filename="General Report.xls";
            $writer=new Xlsx($spreadsheet);
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Type: application/vnd.ms-excel");
            $writer->save("php://output");
            exit();
        } catch (\Exception $e) {
            $logger=new Logger('errors');
            $logger->pushHandler(new StreamHandler('Logs/Finance.log', Logger::INFO));
            $logger->warning($e);
            exit();
        }

    }

    public function generateSingleCustomer(){
        $db=Database::connect();
        $from=$this->request->getVar('from');
        $to=$this->request->getVar('to');
        $customer=$this->request->getVar('customerId');
        $customerName=$this->request->getVar('customerName');
        //check dates to prevent errors
        if($to<$from){
            session()->setFlashdata('fail','The to date has to be latest than from date');
            return redirect()->to(base_url('finance/report_select'));
        }
        $paid=$db->query("SELECT * FROM FINANCE WHERE CONFIRMED=1 AND CLEARED=1 and customerId='$customer' AND DATE BETWEEN '$from' and '$to'")->getResultObject();
        $unpaid=$db->query("SELECT * FROM FINANCE WHERE CONFIRMED=1 AND CLEARED=0 and customerId='$customer' AND DATE BETWEEN '$from' and '$to'")->getResultObject();
        $unconfirmed=$db->query("SELECT * FROM FINANCE WHERE CONFIRMED=0 and customerId='$customer' and DATE BETWEEN '$from' and '$to'")->getResultObject();
        $paidSummary=$db->query("SELECT COUNT(*) AS VOLUME,SUM(totalPayable) AS VALUE FROM finance WHERE CONFIRMED=1 and customerId='$customer' AND CLEARED=1 AND DATE BETWEEN '$from' and '$to'")->getResultObject()[0];
        $unpaidSummary=$db->query("SELECT COUNT(*) AS VOLUME,SUM(totalPayable) AS VALUE FROM finance WHERE CONFIRMED=1 and customerId='$customer' AND CLEARED=0 AND DATE BETWEEN '$from' and '$to'")->getResultObject()[0];
        $unconfirmedSummary=$db->query("SELECT COUNT(*) AS VOLUME,SUM(totalPayable) AS VALUE FROM finance WHERE CONFIRMED=0 and customerId='$customer' AND DATE BETWEEN '$from' and '$to'")->getResultObject()[0];
        //paid
        try {
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet=$reader->load('app/Views/reportTemplates/Financial_Report_Template_Single_Customer.xlsx');
            $spreadsheet->setActiveSheetIndex(0); //0=>paid
            $sheet=$spreadsheet->getActiveSheet();
            $sheet->setCellValue('L2',date('Y-M-d',strtotime($from)).' - '.date('Y-M-d',strtotime($to)))
                ->setCellValue('L3',$paidSummary->VOLUME)
                ->setCellValue('L4',$paidSummary->VALUE)
                ->setCellValue('e7',$customerName);
            $colored=array(
                'font'=>array(
                    'bold'=>true
                ),
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFDDC2',
                    ],
                )
            );
            $colored_total=array(
                'font'=>array(
                    'bold'=>true
                ),
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => '60FF7E',
                    ],
                )
            );
            $color=true;
            $currentRow=9;$start=9;
            foreach ($paid as $p){
                //insert new row
                $sheet->insertNewRowBefore($currentRow+1,1);
                if($color){
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored);
                    $color=false;
                }else{
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->getFont()->setBold(true);
                    $color=true;
                }
                $sheet->setCellValue('B'.$currentRow,$p->id)
                    ->setCellValue('C'.$currentRow,$p->customerName)
                    ->setCellValue('d'.$currentRow,$p->proformaNo)
                    ->setCellValue('e'.$currentRow,$p->taxInvoiceNo)
                    ->setCellValue('f'.$currentRow,$p->lpoNo)
                    ->setCellValue('g'.$currentRow,$p->withholdingTax)
                    ->setCellValue('h'.$currentRow,$p->vat)
                    ->setCellValue('i'.$currentRow,$p->totalPayable)
                    ->setCellValue('j'.$currentRow,"CLEARED")
                    ->setCellValue('k'.$currentRow,$p->contactPerson)
                    ->setCellValue('l'.$currentRow,$p->carRegNo)
                    ->setCellValue('m'.$currentRow,$p->phone)
                    ->setCellValue('N'.$currentRow,date('Y-M-d',strtotime($p->date)));
                $currentRow++;
            }
            //totals row
            $sheet->insertNewRowBefore($currentRow+1,1);
            $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored_total)->getFont()->setSize(12);
            $sheet->mergeCells('b'.$currentRow.':c'.$currentRow);
            $sheet->setCellValue('b'.$currentRow,'TOTALS CLEARED');
            $sheet->setCellValue('g'.$currentRow,"=SUM(G$start:G$currentRow)");
            $sheet->setCellValue('h'.$currentRow,"=SUM(H$start:H$currentRow)");
            $sheet->setCellValue('I'.$currentRow,"=SUM(I$start:I$currentRow)");
            //-----------------------------------------end of paid


            //-----------------------------------------start of unpaid
            $spreadsheet->setActiveSheetIndex(1); //1=>unpaid
            $sheet=$spreadsheet->getActiveSheet();
            $sheet->setCellValue('L2',date('Y-M-d',strtotime($from)).' - '.date('Y-M-d',strtotime($to)))
                ->setCellValue('L3',$unpaidSummary->VOLUME)
                ->setCellValue('L4',$unpaidSummary->VALUE)->setCellValue('e7',$customerName);
            $color=true;
            $currentRow=9;$start=9;
            foreach ($unpaid as $p){
                //insert new row
                $sheet->insertNewRowBefore($currentRow+1,1);
                if($color){
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored);
                    $color=false;
                }else{
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->getFont()->setBold(true);
                    $color=true;
                }
                $sheet->setCellValue('B'.$currentRow,$p->id)
                    ->setCellValue('C'.$currentRow,$p->customerName)
                    ->setCellValue('d'.$currentRow,$p->proformaNo)
                    ->setCellValue('e'.$currentRow,$p->taxInvoiceNo)
                    ->setCellValue('f'.$currentRow,$p->lpoNo)
                    ->setCellValue('g'.$currentRow,$p->withholdingTax)
                    ->setCellValue('h'.$currentRow,$p->vat)
                    ->setCellValue('i'.$currentRow,$p->totalPayable)
                    ->setCellValue('j'.$currentRow,"UNCLEARED")
                    ->setCellValue('k'.$currentRow,$p->contactPerson)
                    ->setCellValue('l'.$currentRow,$p->carRegNo)
                    ->setCellValue('m'.$currentRow,$p->phone)
                    ->setCellValue('N'.$currentRow,date('Y-M-d',strtotime($p->date)));
                $currentRow++;
            }
            //totals row
            $sheet->insertNewRowBefore($currentRow+1,1);
            $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored_total)->getFont()->setSize(12);
            $sheet->mergeCells('b'.$currentRow.':c'.$currentRow);
            $sheet->setCellValue('b'.$currentRow,'TOTALS UNCLEARED');
            $sheet->setCellValue('g'.$currentRow,"=SUM(G$start:G$currentRow)");
            $sheet->setCellValue('h'.$currentRow,"=SUM(H$start:H$currentRow)");
            $sheet->setCellValue('I'.$currentRow,"=SUM(I$start:I$currentRow)");

            //-----------------------------------------start of unconfirmed
            $spreadsheet->setActiveSheetIndex(2); //2=>unconfirmed
            $sheet=$spreadsheet->getActiveSheet();
            $sheet->setCellValue('L2',date('Y-M-d',strtotime($from)).' - '.date('Y-M-d',strtotime($to)))
                ->setCellValue('L3',$unconfirmedSummary->VOLUME)
                ->setCellValue('L4',$unconfirmedSummary->VALUE)->setCellValue('e7',$customerName);
            $color=true;
            $currentRow=9;$start=9;
            foreach ($unconfirmed as $p){
                //insert new row
                $sheet->insertNewRowBefore($currentRow+1,1);
                if($color){
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored);
                    $color=false;
                }else{
                    $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->getFont()->setBold(true);
                    $color=true;
                }
                $sheet->setCellValue('B'.$currentRow,$p->id)
                    ->setCellValue('C'.$currentRow,$p->customerName)
                    ->setCellValue('d'.$currentRow,$p->proformaNo)
                    ->setCellValue('e'.$currentRow,$p->taxInvoiceNo)
                    ->setCellValue('f'.$currentRow,$p->lpoNo)
                    ->setCellValue('g'.$currentRow,$p->withholdingTax)
                    ->setCellValue('h'.$currentRow,$p->vat)
                    ->setCellValue('i'.$currentRow,$p->totalPayable)
                    ->setCellValue('j'.$currentRow,"UNCONFIRMED")
                    ->setCellValue('k'.$currentRow,$p->contactPerson)
                    ->setCellValue('l'.$currentRow,$p->carRegNo)
                    ->setCellValue('m'.$currentRow,$p->phone)
                    ->setCellValue('N'.$currentRow,date('Y-M-d',strtotime($p->date)));
                $currentRow++;
            }
            //totals row
            $sheet->insertNewRowBefore($currentRow+1,1);
            $sheet->getStyle('B'.$currentRow.':'.'N'.$currentRow)->applyFromArray($colored_total)->getFont()->setSize(12);
            $sheet->mergeCells('b'.$currentRow.':c'.$currentRow);
            $sheet->setCellValue('b'.$currentRow,'TOTALS UNCONFIRMED');
            $sheet->setCellValue('g'.$currentRow,"=SUM(G$start:G$currentRow)");
            $sheet->setCellValue('h'.$currentRow,"=SUM(H$start:H$currentRow)");
            $sheet->setCellValue('I'.$currentRow,"=SUM(I$start:I$currentRow)");


            //producing the excel
            $filename="Single Customer Report.xls";
            $writer=new Xlsx($spreadsheet);
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Type: application/vnd.ms-excel");
            $writer->save("php://output");
            exit();
        } catch (\Exception $e) {
            $logger=new Logger('errors');
            $logger->pushHandler(new StreamHandler('Logs/Finance.log', Logger::INFO));
            $logger->warning($e);
            exit();
        }

    }

    public function generateOld(){
        $db=Database::connect();
        $from=$this->request->getVar('from');
        $to=$this->request->getVar('to');
        $id=$this->request->getVar('customerId');
        if($id=='*'){
            //confirmed
            $confirmedQuery="SELECT * FROM FINANCE WHERE CONFIRMED=1 and date between '$from' and '$to' ORDER BY DATE DESC";
        }else{

        }
        $results=$db->query($confirmedQuery)->getResultObject();
        $spreadsheet=new Spreadsheet();
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize();
        $sheet=$spreadsheet->getActiveSheet();
        $sheet->setTitle("Confirmed Jobs");
        $sheet->mergeCells("A1:H1");
        $sheet->setCellValue('A1',"CONFIRMED JOBS REPORT FROM $from to $to at JAPAN AUTO CARE")->getStyle("A1:h1")->getFont()->setBold();
        $sheet->setCellValue('A2','ENTRY ID');
        $sheet->setCellValue('B2','PROFORMA NO');
        $sheet->getColumnDimension('B')->getAutoSize();
        $sheet->setCellValue('C2','TAXINVOICE NO');
        $sheet->getColumnDimension('C')->getAutoSize();
        $sheet->setCellValue('D2','LPO NO');
        $sheet->setCellValue('E2','CUSTOMER NAME');
        $sheet->getColumnDimension('E')->getAutoSize();
        $sheet->setCellValue('F2','CONFIRMED');
        $sheet->getColumnDimension('F')->getAutoSize();
        $sheet->setCellValue('G2','WITH HOLDING TAX');
        $sheet->getColumnDimension('G')->getAutoSize();
        $sheet->setCellValue('H2','VAT');
        $sheet->getColumnDimension('H')->getAutoSize();
        $sheet->setCellValue('I2','TOTAL PAYABLE');
        $sheet->setCellValue('J2','CLEARED');
        $sheet->setCellValue('K2','EMAIL');
        $sheet->setCellValue('L2','PHONE');
        $sheet->setCellValue('M2','CONTACT PERSON');
        $sheet->setCellValue('N2','CAR REG NO');
        $row=3;
        foreach ($results as $r){
            $sheet->setCellValue('A'."$row",$r->id);
            $sheet->setCellValue('B'."$row",$r->proformaNo);
            $sheet->setCellValue('C'."$row",$r->taxInvoiceNo);
            $sheet->setCellValue('D'."$row",$r->lpoNo);
            $sheet->setCellValue('E'."$row",$r->customerName);
            $sheet->setCellValue('F'."$row",$r->confirmed);
            $sheet->setCellValue('G'."$row",$r->withholdingTax);
            $sheet->setCellValue('H'."$row",$r->vat);
            $sheet->setCellValue('I'."$row",$r->totalPayable);
            $sheet->setCellValue('J'."$row",$r->cleared);
            $sheet->setCellValue('K'."$row",$r->email);
            $sheet->setCellValue('L'."$row",$r->phone);
            $sheet->setCellValue('M'."$row",$r->contactPerson);
            $sheet->setCellValue('N'."$row",$r->carRegNo);
            $row++;
        }
        $sheet2=$spreadsheet->createSheet();
        $sheet2->setTitle("holla");

        $writer=new Xlsx($spreadsheet);
        $filename="REPORT.xls";
        try {
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Type: application/vnd.ms-excel");
            $writer->save("php://output");
            exit();
        } catch (Exception $e) {
            print_r($e);
        }
    }
}