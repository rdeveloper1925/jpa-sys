<?php
namespace App\Controllers;

use CodeIgniter\Session\Session;
use Config\Database;
use Config\Services;
use Mpdf\Mpdf;
use Mpdf\Tag\P;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reports extends BaseController {
    public function index(){
        $db=Database::connect();
        $customers=$db->table('customers')->select('id,customerName')->get()->getResult('object');
        return view('reports/selection', ['title'=>'Generate Report','customers'=>$customers]);
    }

    public function generate(){
        $db=Database::connect();
        $reportType=$this->request->getVar('reportType');
        $from=date('Y-m-d',strtotime($this->request->getVar('from')));
        $customer=$this->request->getVar('customer');
        //return print_r($customer);
        $to=date('Y-m-d',strtotime($this->request->getVar('to')));
        if($from>$to){
            return view('reports/selection', ['title'=>'Generate Report','error'=>"from date cannot be after to date"]);
        }
        if($reportType=='Proforma Report'){
            if ($customer!='All'){
                $r=$db->query("SELECT PROFORMA.INVOICEID AS ID,
                           PROFORMA.DATE AS DATE,
                           PROFORMA.carRegNo AS REG,
                           CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,
                           PROFORMA.carRegNo AS REGNO,
                           PROFORMA.lpoNo AS LPO,
                           PROFORMA.narration AS NARRATION,
                           PROFORMA.CURRENCY AS CURRENCY FROM PROFORMA
                        LEFT JOIN CUSTOMERS ON PROFORMA.customerId=customers.id WHERE PROFORMA.DATE BETWEEN '$from' and '$to'
                        AND PROFORMA.customerId=$customer")->getResultArray();
            }
            else{
                $r=$db->query("SELECT PROFORMA.INVOICEID AS ID,
                           PROFORMA.DATE AS DATE,
                           PROFORMA.carRegNo AS REG,
                           CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,
                           PROFORMA.carRegNo AS REGNO,
                           PROFORMA.lpoNo AS LPO,
                           PROFORMA.narration AS NARRATION,
                           PROFORMA.CURRENCY AS CURRENCY FROM PROFORMA
                        LEFT JOIN CUSTOMERS ON PROFORMA.customerId=customers.id WHERE PROFORMA.DATE BETWEEN '$from' and '$to'")->getResultArray();

            }

            foreach($r as $key=>$rr){
                $id=$rr['ID'];
                $sum=$db->query("SELECT SUM(TOTAL) AS SM FROM PROFORMAITEMS2 WHERE INVOICEID='$id'")->getResultArray()[0];
                $vat=((18/100)*$sum['SM']);
                $r[$key]['vat']=$vat;
                $r[$key]['sum']=$sum;
            }
            $data['data']=$r;
            $data['ttl']='PROFORMA INVOICE REPORT';
            $data['from']=$from;
            $data['title']='PROFORMA INVOICE REPORT';
            $data['to']=$to;
            $data['customer']=$customer;
            return view('reports/view_report',$data);
        }
        else{
            if ($customer!='All'){
                $r=$db->query("SELECT INVOICE.INVOICEID AS ID,
                           INVOICE.DATE AS DATE,
                           INVOICE.carRegNo AS REG,
                           CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,
                           INVOICE.carRegNo AS REGNO,
                           INVOICE.lpoNo AS LPO,
                           INVOICE.narration AS NARRATION,
                           INVOICE.CURRENCY AS CURRENCY FROM INVOICE
                        LEFT JOIN CUSTOMERS ON INVOICE.customerId=customers.id WHERE INVOICE.DATE BETWEEN '$from' and '$to'
                        AND INVOICE.customerId=$customer")->getResultArray();
            }
            else{
                $r=$db->query("SELECT INVOICE.INVOICEID AS ID,
                           INVOICE.DATE AS DATE,
                           INVOICE.carRegNo AS REG,
                           CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,
                           INVOICE.carRegNo AS REGNO,
                           INVOICE.lpoNo AS LPO,
                           INVOICE.narration AS NARRATION,
                           INVOICE.CURRENCY AS CURRENCY FROM INVOICE
                        LEFT JOIN CUSTOMERS ON INVOICE.customerId=customers.id WHERE INVOICE.DATE BETWEEN '$from' and '$to'")->getResultArray();

            }
            foreach($r as $key=>$rr){
                $id=$rr['ID'];
                $sum=$db->query("SELECT SUM(TOTAL) AS SM FROM INVOICEITEMS2 WHERE INVOICEID='$id'")->getResultArray()[0];
                $vat=((18/100)*$sum['SM']);
                $r[$key]['vat']=$vat;
                $r[$key]['sum']=$sum;
            }
            $data['data']=$r;
            $data['ttl']='TAX INVOICE REPORT';
            $data['from']=$from;
            $data['title']='TAX INVOICE REPORT';
            $data['to']=$to;
            $data['customer']=$customer;
            return view('reports/view_report',$data);
        }
    }

    public function write_to_pdf($r,$ttl,$from,$to){
        $data['data']=$r;
        $data['ttl']=$ttl;
        $data['ttl2']="FROM ".date('d-M-Y',strtotime($from))." TO ".date('d-M-Y',strtotime($to));
        $data['from']=$from;
        $data['to']=$to;
        //return print_r($data['data']);
        $pdf=new Mpdf();
        $pdf->SetMargins(15,15,15,15);
        $pdf->SetWatermarkImage(base_url("assets/img/logo.png"),0.3,'F','F');
        $pdf->SetHTMLHeader('<div style="border-bottom: 1px solid #000000;">'.$ttl.'</div>','E');
        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
    								
    									<tr>
											<td width="33%">'.date("d-M-Y").'</td>
											<td width="33%" align="center">"Customer Satisfaction First"</td>
											<td width="33%" align="center">Page: {PAGENO}/{nbpg}</td>
    									</tr>
									</table>');  // Note that the second parameter is optional : default = 'O' for ODD

        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
										<tr>
											<td width="33%"><span style="font-weight: bold; font-style: italic;">My document</span></td>
											<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
											<td width="33%" style="text-align: right; "></td>
										</tr>
									</table>', 'E');

        $pdf->WriteHTML(view('reports/report_pdf',$data));
        $pdf->Output("Report".date('Y-m-d').".pdf","D");
        return ;
    }

    public function excel($from,$to,$customer){
        $db=Database::connect();
        if($customer=='All'){
            $r=$db->query("SELECT PROFORMA.INVOICEID AS ID,PROFORMA.DATE AS DATE,PROFORMA.carRegNo AS REG,CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,PROFORMA.lpoNo AS LPO,PROFORMA.narration AS NARRATION,PROFORMA.CURRENCY AS CURRENCY FROM PROFORMA LEFT JOIN CUSTOMERS ON PROFORMA.customerId=customers.id WHERE PROFORMA.DATE BETWEEN '$from' and '$to'")->getResultArray();
        }else{
            $r=$db->query("SELECT PROFORMA.INVOICEID AS ID,PROFORMA.DATE AS DATE,PROFORMA.carRegNo AS REG,CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,PROFORMA.lpoNo AS LPO,PROFORMA.narration AS NARRATION,PROFORMA.CURRENCY AS CURRENCY FROM PROFORMA LEFT JOIN CUSTOMERS ON PROFORMA.customerId=customers.id WHERE PROFORMA.DATE BETWEEN '$from' and '$to' AND PROFORMA.customerId=$customer")->getResultArray();
        }
        $spreadsheet=new Spreadsheet();
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->getAutoSize();
        $sheet=$spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:H1");
        $sheet->setCellValue('A1',"PROFORMA INVOICE REPORT FROM $from, TO $to FROM JAPAN AUTO CARE");
        $sheet->setCellValue('A2','INVOICE ID');
        $sheet->setCellValue('B2','BUSINESS PARTNER');
        $sheet->getColumnDimension('B')->getAutoSize();
        $sheet->setCellValue('C2','DATE');
        $sheet->getColumnDimension('C')->getAutoSize();
        $sheet->setCellValue('D2','VAT');
        $sheet->setCellValue('E2','WITHHOLDING TAX');
        $sheet->getColumnDimension('E')->getAutoSize();
        $sheet->setCellValue('F2','NARRATION');
        $sheet->getColumnDimension('F')->getAutoSize();
        $sheet->setCellValue('G2','LPO');
        $sheet->getColumnDimension('G')->getAutoSize();
        $sheet->setCellValue('H2','VALUE');
        $sheet->getColumnDimension('H')->getAutoSize();
        $frm = "A1"; // or any value
        $t = "G2"; // or any value
        $spreadsheet->getActiveSheet()->getStyle("$frm:$t")->getFont()->setBold( true );
        foreach($r as $key=>$rr){
            $id=$rr['ID'];
            $sum=$db->query("SELECT SUM(TOTAL) AS SM FROM PROFORMAITEMS2 WHERE INVOICEID='$id'")->getResultArray()[0];
            $vat=((18/100)*$sum['SM']);
            $r[$key]['vat']=$vat;
            $r[$key]['sum']=$sum['SM'];
            $sheet->setCellValue('A'.($key+3),$rr['ID']);
            $sheet->setCellValue('B'.($key+3),$rr['CUSTOMER_NAME']);
            $sheet->setCellValue('C'.($key+3),$rr['DATE']);
            $sheet->setCellValue('D'.($key+3),$vat);
            if ($sum['SM']>=1000000){
                $wtax=(6/100)*$sum['SM'];
                $sheet->setCellValue('E'.($key+3),$wtax);
            }else{
                $sheet->setCellValue('E'.($key+3),0);
            }
            $sheet->setCellValue('F'.($key+3),$rr['NARRATION']);
            $sheet->setCellValue('G'.($key+3),$rr['LPO']);
            $sheet->setCellValue('H'.($key+3),$sum['SM']);
        }
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

    public function tax_excel($from,$to,$customer){
        $db=Database::connect();
        if($customer=='All'){
            $r=$db->query("SELECT invoice.INVOICEID AS ID,invoice.DATE AS DATE,invoice.carRegNo AS REG,CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,invoice.lpoNo AS LPO,invoice.narration AS NARRATION,invoice.CURRENCY AS CURRENCY FROM invoice LEFT JOIN CUSTOMERS ON invoice.customerId=customers.id WHERE invoice.DATE BETWEEN '$from' and '$to'")->getResultArray();
        }else{
            $r=$db->query("SELECT invoice.INVOICEID AS ID,invoice.DATE AS DATE,invoice.carRegNo AS REG,CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,invoice.lpoNo AS LPO,invoice.narration AS NARRATION,invoice.CURRENCY AS CURRENCY FROM invoice LEFT JOIN CUSTOMERS ON invoice.customerId=customers.id WHERE invoice.DATE BETWEEN '$from' and '$to' AND invoice.customerId=$customer")->getResultArray();
        }
        $spreadsheet=new Spreadsheet();
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->getAutoSize();
        $sheet=$spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:H1");
        $sheet->setCellValue('A1',"TAX INVOICE REPORT FROM $from, TO $to FROM JAPAN AUTO CARE");
        $sheet->setCellValue('A2','INVOICE ID');
        $sheet->setCellValue('B2','BUSINESS PARTNER');
        $sheet->getColumnDimension('B')->getAutoSize();
        $sheet->setCellValue('C2','DATE');
        $sheet->getColumnDimension('C')->getAutoSize();
        $sheet->setCellValue('D2','VAT');
        $sheet->getColumnDimension('D')->getAutoSize();
        $sheet->setCellValue('E2','WITHHOLDING TAX');
        $sheet->getColumnDimension('E')->getAutoSize();
        $sheet->setCellValue('F2','NARRATION');
        $sheet->getColumnDimension('F')->getAutoSize();
        $sheet->setCellValue('G2','LPO');
        $sheet->getColumnDimension('G')->getAutoSize();
        $sheet->setCellValue('H2','VALUE');
        $sheet->getColumnDimension('H')->getAutoSize();
        $frm = "A1"; // or any value
        $t = "G2"; // or any value
        $spreadsheet->getActiveSheet()->getStyle("$frm:$t")->getFont()->setBold( true );
        foreach($r as $key=>$rr){
            $id=$rr['ID'];
            $sum=$db->query("SELECT SUM(TOTAL) AS SM FROM PROFORMAITEMS2 WHERE INVOICEID='$id'")->getResultArray()[0];
            $vat=((18/100)*$sum['SM']);
            $r[$key]['vat']=$vat;
            $r[$key]['sum']=$sum['SM'];
            $sheet->setCellValue('A'.($key+3),$rr['ID']);
            $sheet->setCellValue('B'.($key+3),$rr['CUSTOMER_NAME']);
            $sheet->setCellValue('C'.($key+3),$rr['DATE']);
            $sheet->setCellValue('D'.($key+3),$vat);
            if ($sum['SM']>=1000000){
                $wtax=(6/100)*$sum['SM'];
                $sheet->setCellValue('E'.($key+3),$wtax);
            }else{
                $sheet->setCellValue('E'.($key+3),0);
            }
            $sheet->setCellValue('F'.($key+3),$rr['NARRATION']);
            $sheet->setCellValue('G'.($key+3),$rr['LPO']);
            $sheet->setCellValue('H'.($key+3),$sum['SM']);
        }
        $writer=new Xlsx($spreadsheet);
        $filename="TAX INVOICE REPORT.xlsx";
        try {
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Type: application/vnd.ms-excel");
            $writer->save("php://output");
            exit();
        } catch (Exception $e) {
            print_r($e);
        }

    }

    public function pdf($from,$to,$customer){
        $db=Database::connect();
        if($customer=='All'){
            $r=$db->query("SELECT PROFORMA.INVOICEID AS ID,PROFORMA.DATE AS DATE,PROFORMA.carRegNo AS REG,CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,PROFORMA.lpoNo AS LPO,PROFORMA.narration AS NARRATION,PROFORMA.CURRENCY AS CURRENCY FROM PROFORMA LEFT JOIN CUSTOMERS ON PROFORMA.customerId=customers.id WHERE PROFORMA.DATE BETWEEN '$from' and '$to'")->getResultArray();
        }else{
            $r=$db->query("SELECT PROFORMA.INVOICEID AS ID,PROFORMA.DATE AS DATE,PROFORMA.carRegNo AS REG,CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,PROFORMA.lpoNo AS LPO,PROFORMA.narration AS NARRATION,PROFORMA.CURRENCY AS CURRENCY FROM PROFORMA LEFT JOIN CUSTOMERS ON PROFORMA.customerId=customers.id WHERE PROFORMA.DATE BETWEEN '$from' and '$to' AND PROFORMA.customerId=$customer")->getResultArray();
        }
        $data['ttl']='PROFORMA INVOICE REPORT';
        $data['ttl2']="FROM ".date('d-M-Y',strtotime($from))." TO ".date('d-M-Y',strtotime($to));

        foreach($r as $key=>$rr){
            $id=$rr['ID'];
            $sum=$db->query("SELECT SUM(TOTAL) AS SM FROM PROFORMAITEMS2 WHERE INVOICEID='$id'")->getResultArray()[0];
            $vat=((18/100)*$sum['SM']);
            $r[$key]['vat']=$vat;
            $r[$key]['sum']=$sum['SM'];
        }
        $data['data']=$r;
        //return print_r($r);
        $pdf=new Mpdf();
        $pdf->SetMargins(15,15,15,15);
        $pdf->SetWatermarkImage(base_url("assets/img/logo.png"),0.3,'F','F');
        $pdf->SetHTMLHeader('<div style="border-bottom: 1px solid #000000;">PROFORMA INVOICE REPORT</div>','E');
        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
    								
    									<tr>
											<td width="33%">'.date("d-M-Y").'</td>
											<td width="33%" align="center">"Customer Satisfaction First"</td>
											<td width="33%" align="center">Page: {PAGENO}/{nbpg}</td>
    									</tr>
									</table>');  // Note that the second parameter is optional : default = 'O' for ODD

        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
										<tr>
											<td width="33%"><span style="font-weight: bold; font-style: italic;">My document</span></td>
											<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
											<td width="33%" style="text-align: right; "></td>
										</tr>
									</table>', 'E');

        $pdf->WriteHTML(view('reports/report_pdf',$data));
        $pdf->Output("Proforma Report".date('Y-m-d').".pdf","D");
        return ;
    }

    public function tax_pdf($from,$to,$customer){
        $db=Database::connect();
        if($customer=='All'){
            $r=$db->query("SELECT INVOICE.INVOICEID AS ID,INVOICE.DATE AS DATE,INVOICE.carRegNo AS REG,CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,INVOICE.lpoNo AS LPO,INVOICE.narration AS NARRATION,INVOICE.CURRENCY AS CURRENCY FROM INVOICE LEFT JOIN CUSTOMERS ON INVOICE.customerId=customers.id WHERE INVOICE.DATE BETWEEN '$from' and '$to'")->getResultArray();
        }else{
            $r=$db->query("SELECT INVOICE.INVOICEID AS ID,INVOICE.DATE AS DATE,INVOICE.carRegNo AS REG,CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,INVOICE.lpoNo AS LPO,INVOICE.narration AS NARRATION,INVOICE.CURRENCY AS CURRENCY FROM INVOICE LEFT JOIN CUSTOMERS ON INVOICE.customerId=customers.id WHERE INVOICE.DATE BETWEEN '$from' and '$to' AND INVOICE.customerId=$customer")->getResultArray();
        }
        $data['ttl']='TAX INVOICE REPORT';
        $data['ttl2']="FROM ".date('d-M-Y',strtotime($from))." TO ".date('d-M-Y',strtotime($to));

        foreach($r as $key=>$rr){
            $id=$rr['ID'];
            $sum=$db->query("SELECT SUM(TOTAL) AS SM FROM INVOICEITEMS2 WHERE INVOICEID='$id'")->getResultArray()[0];
            $vat=((18/100)*$sum['SM']);
            $r[$key]['vat']=$vat;
            $r[$key]['sum']=$sum['SM'];
        }
        $data['data']=$r;
        //return print_r($r);
        $pdf=new Mpdf();
        $pdf->SetMargins(15,15,15,15);
        $pdf->SetWatermarkImage(base_url("assets/img/logo.png"),0.3,'F','F');
        $pdf->SetHTMLHeader('<div style="border-bottom: 1px solid #000000;">TAX INVOICE REPORT</div>','E');
        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
    								
    									<tr>
											<td width="33%">'.date("d-M-Y").'</td>
											<td width="33%" align="center">"Customer Satisfaction First"</td>
											<td width="33%" align="center">Page: {PAGENO}/{nbpg}</td>
    									</tr>
									</table>');  // Note that the second parameter is optional : default = 'O' for ODD

        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
										<tr>
											<td width="33%"><span style="font-weight: bold; font-style: italic;">My document</span></td>
											<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
											<td width="33%" style="text-align: right; "></td>
										</tr>
									</table>', 'E');

        $pdf->WriteHTML(view('reports/report_pdf',$data));
        $pdf->Output("Tax Invoice Report".date('Y-m-d').".pdf","D");
        return ;
    }
}