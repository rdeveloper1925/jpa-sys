<?php namespace App\Controllers;

use Config\Database;
use Dompdf\Dompdf;
use Mpdf\Mpdf;

class Pages extends BaseController
{
	public function index(){
		$db=Database::connect();
		$data['invoices']=$db->table('invoice')->countAll();
		$data['receipts']=$db->table('receipt')->countAll();
		$data['inventory']=$db->table('inventory')->countAll();
		$data['proforma']=$db->table('proforma')->countAll();
        $data['certificates']=$db->table('certificates')->countAll();
        $data['suppliers']=$db->table('suppliers')->countAll();
		$data['title']="Home Page";
		$data['users']=$db->table('users')->countAll();
		return view('content/home',$data);
	}

	public function error(){
        return view('error',['title'=>'UNAUTHORIZED ACCESS','message'=>'You are not allowed to view this page']);
    }

	public function pdf(){
		$id=21329;
		$db=Database::connect();
		$data['items']=$db->table('invoiceitems')->getWhere(['invoiceId'=>$id])->getResultArray();
		$data['discount']=$db->table('discounts')->getWhere(['invoiceId'=>$id])->getResultArray();
		$data['data']=$db->table('invoice')->getWhere(['invoiceId'=>$id])->getResult('object')[0];
		$data['invoiceId']=$id;
		$pdf=new Dompdf();
		$html_content =view('html_convert_pdf',$data);
		;
		$pdf->loadHtml($html_content);
		$pdf->render();
		$pdf->stream("Rick.pdf", array("Attachment"=>0));
	}
	public function mpdf(){
		$pdf=new Mpdf();
		$html=view('html_convert_pdf');
		$pdf->WriteHTML($html);
		$pdf->Output("mine.pdf",'I');
	}
	public function mp(){
		$mpdf=new Mpdf();
		$mpdf->SetHTMLHeader('
<div style="text-align: right; font-weight: bold;">
    My document
</div>','O');
		$mpdf->SetHTMLHeader('<div style="border-bottom: 1px solid #000000;">My document</div>','E');

		$mpdf->SetHTMLFooter('
<table width="100%" style="vertical-align: bottom; font-family: serif; 
    font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
    <tr>
        <td width="33%">{DATE j-m-Y}</td>
        <td width="33%" align="center">{PAGENO}/{nbpg}</td>
        <td width="33%" style="text-align: right;">My document</td>
    </tr>
</table>');  // Note that the second parameter is optional : default = 'O' for ODD

		$mpdf->SetHTMLFooter('
<table width="100%" style="vertical-align: bottom; font-family: serif; 
    font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
    <tr>
        <td width="33%"><span style="font-weight: bold; font-style: italic;">My document</span></td>
        <td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
        <td width="33%" style="text-align: right; ">{DATE j-m-Y}</td>
    </tr>
</table>', 'E');

		$mpdf->WriteHTML(view('html_convert_pdf'));

		$mpdf->Output("mine.pdf","D");
	}

}
