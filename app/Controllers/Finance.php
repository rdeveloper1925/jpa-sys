<?php
namespace App\Controllers;
use Config\Database;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use org\bovigo\vfs\vfsStreamContainerIterator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Finance extends BaseController{
    public function match(){
        $db=Database::connect();
        $proformae=$db->table('proforma');
    }

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
        $entry=array(
            'date'=>$this->request->getVar('date'),
            'proformaNo'=>$this->request->getVar('proformaNo'),
            'taxInvoiceNo'=>$this->request->getVar('taxInvoiceNo'),
            'lpoNo'=>$this->request->getVar('lpoNo'),
            'customerId'=>$this->request->getVar('customerId'),
            'customerName'=>$this->request->getVar('customerName'),
            'confirmed'=>$this->request->getVar('confirmed'),
            'withholdingTax'=>$this->request->getVar('withholdingTax'),
            'vat'=>$this->request->getVar('vat'),
            'totalPayable'=>$this->request->getVar('totalPayable'),
            'cleared'=>$this->request->getVar('cleared'),
            'email'=>$this->request->getVar('email'),
            'phone'=>$this->request->getVar('phone'),
            'areaCountry'=>$this->request->getVar('areaCountry'),
            'address'=>$this->request->getVar('address'),
            'tinNo'=>$this->request->getVar('tinNo'),
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
        $entry=array(
           // 'date'=>$this->request->getVar('date'),
            'proformaNo'=>$this->request->getVar('proformaNo'),
            'taxInvoiceNo'=>$this->request->getVar('taxInvoiceNo'),
            'lpoNo'=>$this->request->getVar('lpoNo'),
            'customerId'=>$this->request->getVar('customerId'),
            'customerName'=>$this->request->getVar('customerName'),
            'confirmed'=>$this->request->getVar('confirmed'),
            'withholdingTax'=>$this->request->getVar('withholdingTax'),
            'vat'=>$this->request->getVar('vat'),
            'totalPayable'=>$this->request->getVar('totalPayable'),
            'cleared'=>$this->request->getVar('cleared'),
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
        $customers=$db->table('customers')->select('customerName,id')->get()->getResultObject();
        return view('finance/report_select',['title'=>'Select Report','customers'=>$customers]);
    }

    public function view_report(){
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/Finance.log', Logger::INFO));
        $db=Database::connect();
        $customer=$this->request->getVar('customer');
        $reportType=$this->request->getVar('reportType');
        $selectedReport='';
        if($customer != '*') {//if customer is specific
            switch ($reportType) {//checking report type and generating it
                case 'confirmed':
                    $report = $db->table('finance')->select('count(*) as confirmed')->getWhere(['confirmed' => 1,'customerId'=>$customer])->getResultObject()[0];
                    $selectedReport= 'Confirmed';
                    return json_encode($report);
                    break;
                case 'unconfirmed':
                    $report = $db->table('finance')->getWhere(['confirmed' => 0,'customerId'=>$customer])->getResultObject();
                    $selectedReport= 'Not Confirmed';
                    break;
                case 'cleared':
                    $report = $db->table('finance')->getWhere(['cleared' => 1,'customerId'=>$customer])->getResultObject();
                    $selectedReport= 'Cleared';
                    break;
                case 'uncleared':
                    $report = $db->table('finance')->getWhere(['cleared' => 0,'customerId'=>$customer])->getResultObject();
                    $selectedReport= 'Not Cleared';
                    break;
                case 'confirmedUncleared':
                    $report = $db->table('finance')->getWhere(['confirmed' => 1,'customerId'=>$customer, 'cleared' => 0])->getResultObject();
                    $selectedReport= 'Confirmed but Not Cleared';
                    break;
                case 'confirmedCleared':
                    $report = $db->table('finance')->getWhere(['confirmed' => 1,'customerId'=>$customer, 'cleared' => 1])->getResultObject();
                    $selectedReport= 'Confirmed and Cleared';
                    break;
                case '*':
                    $report = $db->table('finance')->getWhere(['customerId'=>$customer])->getResultObject();
                    $selectedReport= 'All';
                    break;
                default:
                    return view('error',['message'=>'Unknown case']);
            }
            //metrics= total, number of selected type,
            $data['title']='Single Customer, '.$selectedReport.' Report';
            $data['report']=$report;
            $data['selectedReport']=$selectedReport;
            return view('visuals/index',$data);

        } else{//if all customers are required
            switch ($reportType) {//checking report and generating it
                case 'confirmed':
                    $report = $db->table('finance')->getWhere(['confirmed' => 1])->getResultObject();
                    $selectedReport= 'Confirmed';
                    break;
                case 'unconfirmed':
                    $report = $db->table('finance')->getWhere(['confirmed' => 0])->getResultObject();
                    $selectedReport= 'Not Confirmed';
                    break;
                case 'cleared':
                    $report = $db->table('finance')->getWhere(['cleared' => 1])->getResultObject();
                    $selectedReport= 'Cleared';
                    break;
                case 'uncleared':
                    $report = $db->table('finance')->getWhere(['cleared' => 0])->getResultObject();
                    $selectedReport= 'Not Cleared';
                    break;
                case 'confirmedUncleared':
                    $report = $db->table('finance')->getWhere(['confirmed' => 1, 'cleared' => 0])->getResultObject();
                    $selectedReport= 'Confirmed but Not Cleared';
                    break;
                case 'confirmedCleared':
                    $report = $db->table('finance')->getWhere(['confirmed' => 1, 'cleared' => 1])->getResultObject();
                    $selectedReport= 'Confirmed and Cleared';
                    break;
                case '*':
                    //$report = $db->table('finance')->get()->getResultObject();
                    //$selectedReport= 'All';
                    return redirect()->to(base_url('finance/allCustomersAllReports'));
                    break;
                default:
                    return view('error',['message'=>'Unknown case']);
            }
            return json_encode($report);
        }
        //return json_encode($this->request->getPost());
        return view('visuals/index',['title'=>'visualizations']);
    }

    public function allCustomersAllReports(){
        $db=Database::connect();
        $confirmed=$db->query('SELECT COUNT(*) AS VOLUME,SUM(totalPayable) AS VALUE FROM finance WHERE CONFIRMED=1')->getResultObject()[0];
        $cleared=$db->query('SELECT COUNT(*) AS VOLUME, SUM(totalPayable) AS VALUE FROM finance WHERE CLEARED=1')->getResultObject()[0];
        $confirmedAndCleared=$db->query('SELECT COUNT(*) AS VOLUME, SUM(totalPayable) AS VALUE FROM finance WHERE CLEARED=1 AND confirmed=1')->getResultObject()[0];
        $unconfirmedAndUnCleared=$db->query('SELECT COUNT(*) AS VOLUME, SUM(totalPayable) AS VALUE FROM finance WHERE CLEARED=0 AND confirmed=0')->getResultObject()[0];

        $data['title']="Showing Information for All Customers and All Reports";
        $data['entries']=$db->table('finance')->countAll();
        $data['entryValue']=$db->query('SELECT SUM(totalPayable) as VALUE FROM FINANCE')->getResultObject()[0]->VALUE;
        $data['confirmedVolume']=$confirmed->VOLUME;
        $data['confirmedValue']=$confirmed->VALUE;
        $data['unconfirmedVolume']=$data['entries']-$confirmed->VOLUME;
        $data['unconfirmedValue']=$data['entryValue']-$confirmed->VALUE;
        $data['clearedVolume']=$cleared->VOLUME;
        $data['clearedValue']=$cleared->VALUE;
        $data['unclearedVolume']=$data['entries']-$cleared->VOLUME;
        $data['unclearedValue']=$data['entryValue']-$cleared->VALUE;
        $data['confirmedAndClearedVolume']=$confirmedAndCleared->VOLUME;
        $data['confirmedAndClearedValue']=$confirmedAndCleared->VALUE;
        $data['confirmedAndUnClearedVolume']=$data['entries']-$confirmedAndCleared->VOLUME;
        $data['confirmedAndUnClearedValue']=$data['entryValue']-$confirmedAndCleared->VALUE;
        $data['unconfirmedAndUnClearedVolume']=$unconfirmedAndUnCleared->VOLUME;
        $data['unconfirmedAndUnClearedValue']=$unconfirmedAndUnCleared->VALUE;
        $data['trend']=$db->query("select count(*) as VOLUME,sum(totalPayable) AS VALUE, year(date) AS YEAR, date_format(date,'%b') AS MONTH from finance where cleared=1 group by year(date),month(date) order by year(date) desc LIMIT 12")->getResultObject();
        $data['trendMaxValue']=$db->query("select max(VALUE) AS MAX_VALUE FROM (SELECT sum(totalPayable) AS VALUE FROM FINANCE finance where cleared=1 group by year(date),month(date) ORDER BY YEAR(DATE) DESC LIMIT 12) AS FIN2;")->getResultObject()[0]->MAX_VALUE+400000;
        $data['customerId']='*';
        return view('visuals/index',$data);
    }

    public function generate(){
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
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->getAutoSize();
        $sheet=$spreadsheet->getActiveSheet();
        $sheet->setTitle("Confirmed Jobs");
        $sheet->mergeCells("A1:H1");
        $sheet->setCellValue('A1',"CONFIRMED JOBS REPORT FROM $from to $to at JAPAN AUTO CARE");
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
        $filename="REPORT.xlsx";
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