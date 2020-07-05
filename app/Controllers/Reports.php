<?php
namespace App\Controllers;

use CodeIgniter\Session\Session;
use Config\Database;
use Config\Services;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reports extends BaseController {
    public function index(){
        return view('reports/selection', ['title'=>'Generate Report']);
    }

    public function generate(){
        $db=Database::connect();
        $reportType=$this->request->getVar('reportType');
        $from=$this->request->getVar('from');
        $to=$this->request->getVar('to');
        if($from>$to){
            return view('reports/selection', ['title'=>'Generate Report','error'=>"from date cannot be after to date"]);
        }
        if($reportType=='Proforma Report'){
            //proforma
            /*
            $rs=$db->query("SELECT PROFORMA.INVOICEID AS ID,
                                    PROFORMA.DATE AS DATE, 
                                    PROFORMA.carRegNo AS REG, 
                                    CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,
                                    PROFORMA.CURRENCY AS CURRENCY,
                                    SUM(PROFORMAITEMS2.TOTAL) AS TOTALS FROM PROFORMA INNER JOIN CUSTOMERS ON PROFORMA.customerId=customers.id LEFT JOIN PROFORMAITEMS2 ON 
                                    PROFORMAITEMS2.INVOICEID=PROFORMA.INVOICEID WHERE PROFORMA.DATE BETWEEN '$from' and '$to'")->getResult('object');
            */
            $r=$db->query("SELECT PROFORMA.INVOICEID AS ID,
                           PROFORMA.DATE AS DATE,
                           PROFORMA.carRegNo AS REG,
                           CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,
                           PROFORMA.carRegNo AS REGNO,
                           PROFORMA.lpoNo AS LPO,
                           PROFORMA.narration AS NARRATION,
                           PROFORMA.CURRENCY AS CURRENCY FROM PROFORMA
                        LEFT JOIN CUSTOMERS ON PROFORMA.customerId=customers.id WHERE PROFORMA.DATE BETWEEN '$from' and '$to'")->getResultArray();
            foreach($r as $key=>$rr){
                $id=$rr['ID'];
                $sum=$db->query("SELECT SUM(TOTAL) AS SM FROM PROFORMAITEMS2 WHERE INVOICEID='$id'")->getResultArray()[0];
                $vat=((18/100)*$sum['SM']);
                $r[$key]['vat']=$vat;
                $r[$key]['sum']=$sum;
            }
            //return print_r($r);
            //$this->write_to_pdf($r,'PPROFORMA INVOICE REPORT',$from,$to);
            $data['data']=$r;
            $data['ttl']='PROFORMA INVOICE REPORT';
            $data['from']=$from;
            $data['title']='PROFORMA INVOICE REPORT';
            $data['to']=$to;
            return view('reports/view_report',$data);
        }else{/*
            $rs=$db->query("SELECT INVOICE.INVOICEID AS ID,
                                    INVOICE.DATE AS DATE, 
                                    INVOICE.carRegNo AS REG, 
                                    CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,
                                    INVOICE.CURRENCY AS CURRENCY,
                                    SUM(INVOICEITEMS2.TOTAL) AS TOTALS FROM INVOICE LEFT JOIN CUSTOMERS ON INVOICE.customerId=customers.id LEFT JOIN INVOICEITEMS2 ON 
                                    INVOICEITEMS2.INVOICEID=INVOICE.INVOICEID WHERE INVOICE.DATE BETWEEN '$from' and '$to'")->getResult('object');
*/
            $r=$db->query("SELECT INVOICE.INVOICEID AS ID,
                                    INVOICE.DATE AS DATE, 
                                    INVOICE.carRegNo AS REG, 
                                    CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,
                                    INVOICE.CURRENCY AS CURRENCY FROM INVOICE LEFT JOIN CUSTOMERS ON INVOICE.customerId=customers.id WHERE INVOICE.DATE BETWEEN '$from' and '$to'")->getResultArray();
            foreach($r as $key=>$rr){
                $id=$rr['ID'];
                $sum=$db->query("SELECT SUM(TOTAL) AS SM FROM INVOICEITEMS2 WHERE INVOICEID='$id'")->getResultArray()[0];
                $r[$key]['sum']=$sum;
            }
            //return print_r($r);
            $this->write_to_pdf($r,'TAX INVOICE REPORT',$from,$to);
            //tax invoice
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

    public function excel($from,$to){
        $db=Database::connect();
        $r=$db->query("SELECT PROFORMA.INVOICEID AS ID,PROFORMA.DATE AS DATE,PROFORMA.carRegNo AS REG,CUSTOMERS.CUSTOMERNAME AS CUSTOMER_NAME,PROFORMA.lpoNo AS LPO,PROFORMA.narration AS NARRATION,PROFORMA.CURRENCY AS CURRENCY FROM PROFORMA LEFT JOIN CUSTOMERS ON PROFORMA.customerId=customers.id WHERE PROFORMA.DATE BETWEEN '$from' and '$to'")->getResultArray();
        $spreadsheet=new Spreadsheet();
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->getAutoSize();
        $sheet=$spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:G1");
        $sheet->setCellValue('A1',"PROFORMA INVOICE REPORT FROM $from, TO $to FROM JAPAN AUTO CARE");
        $sheet->setCellValue('A2','INVOICE ID');
        $sheet->setCellValue('B2','BUSINESS PARTNER');
        $sheet->getColumnDimension('B')->getAutoSize();
        $sheet->setCellValue('C2','DATE');
        $sheet->getColumnDimension('C')->getAutoSize();
        $sheet->setCellValue('D2','VAT');
        $sheet->getColumnDimension('D')->getAutoSize();
        $sheet->setCellValue('E2','NARRATION');
        $sheet->getColumnDimension('E')->getAutoSize();
        $sheet->setCellValue('F2','LPO');
        $sheet->getColumnDimension('F')->getAutoSize();
        $sheet->setCellValue('G2','VALUE');
        $sheet->getColumnDimension('G')->getAutoSize();
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
            $sheet->setCellValue('E'.($key+3),$rr['NARRATION']);
            $sheet->setCellValue('F'.($key+3),$rr['LPO']);
            $sheet->setCellValue('G'.($key+3),$sum['SM']);
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
    public function xltest(){
        $spreadsheet=new Spreadsheet();
        $sheet=$spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1','hiii');
        $writer=new Xlsx($spreadsheet);
        $filename="ola.xlsx";
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