<?php
namespace App\Controllers;

use Config\Database;
use Config\Services;
use Mpdf\Mpdf;

class Certificates extends BaseController{
    public function index(){
            $db=Database::connect();
            $certificates=$db->table('certificates')->orderBy('id', 'ASC')->get()->getResult('object');
            return view('certificates/index', ['title'=>'Completion Certificates', 'certificates'=>$certificates]);
        return redirect()->to(base_url('pages/error'));
    }

    public function new(){
        $db=Database::connect();
        $customers=$db->table('customers')->get()->getResultObject();
        return view('certificates/new',['title'=>"New Completion Certificate",'customers'=>$customers]);
    }

    public function save(){
        $certificate=array(
            'customerName'=>$this->request->getVar('customerName'),
            'phone'=>$this->request->getVar('phone'),
            'carType'=>$this->request->getVar('carType'),
            'carRegNo'=>$this->request->getVar('carRegNo'),
            'carChasisNo'=>$this->request->getVar('carChasisNo'),
            'invoiceNo'=>$this->request->getVar('invoiceNo'),
            'contactPerson'=>$this->request->getVar('contactPerson'),
            'mileage'=>$this->request->getVar('mileage'),
            'engineerName'=>$this->request->getVar('engineerName'),
            'repairsDone'=>$this->request->getVar('repairsDone'),
            'transportOfficer'=>$this->request->getVar('transportOfficer'),
            'driverName'=>$this->request->getVar('driverName'),
            'dateCompleted'=>$this->request->getVar('dateCompleted'),
            'comments'=>$this->request->getVar('comments'),
            'address'=>$this->request->getVar('address'),
            'country'=>$this->request->getVar('country'),
            'email'=>$this->request->getVar('email'),
            'tinNo'=>$this->request->getVar('tinNo'),
        );
        $db=Database::connect();
        $db->table('certificates')->insert($certificate);
        $id=$db->insertID();
        //return print_r($certificate);
        return redirect()->to(base_url('inventory/view/'.$id));
    }

    public function view($id){
        $db=Database::connect();
        $certificate=$db->table('certificates')->where('id',$id)->get()->getResultObject()[0];
        //print_r($certificate);
        return view('certificates/view',['title'=>'View Certificate','certificate'=>$certificate]);
    }

    public function print($id){
        $db=Database::connect();
        $certificate=$db->table('certificates')->where('id',$id)->get()->getResultObject()[0];
    }

    public function generate($id){
        $db=Database::connect();
        $data['ttl']="TAX INVOICE";
        $data['words']=$this->request->getVar('words');
        $data['items']=$db->table('invoiceitems')->getWhere(['invoiceId'=>$id])->getResultArray();
        $data['items']=$db->table('invoiceitems2')->select('*')
            ->getWhere(['invoiceId'=>$id])->getResultArray();
        $data['discount']=$db->table('discounts')->getWhere(['invoiceId'=>$id])->getResultArray();
        //$data['data']=$db->table('invoice')->getWhere(['invoiceId'=>$id])->getResult('object')[0];
        $data['invoiceId']=$id;
        $data['data']=$db->table('invoice')->
        select('*')->join('customers','invoice.customerId=customers.id','inner')
            ->join('users','invoice.preparedBy=users.id','inner')
            ->orderBy('date','DESC')->getWhere(['invoiceId'=>$id])->getResult('object')[0];
        $dta=$data['data'];
        $pdf=new Mpdf();
        $pdf->SetMargins(15,15,15,15);
        $pdf->SetWatermarkImage(base_url("assets/img/logo.png"),0.3,'F','F');
        $pdf->SetHTMLHeader('<div style="border-bottom: 1px solid #000000;">TAX INVOICE</div>','E');
        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
    								
    									<tr>
											<td width="33%">'.date("d-M-Y",strtotime($dta->date)).'</td>
											<td width="33%" align="center">"Customer Satisfaction First"</td>
											<td width="33%" align="center">Page: {PAGENO}/{nbpg}</td>
    									</tr>
									</table>');  // Note that the second parameter is optional : default = 'O' for ODD

        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
										<tr>
											<td width="33%"><span style="font-weight: bold; font-style: italic;">My document</span></td>
											<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
											<td width="33%" style="text-align: right; ">'.date("d-M-Y",strtotime($dta->date)).'</td>
										</tr>
									</table>', 'E');

        $pdf->WriteHTML(view('html_convert_pdf',$data));
        $pdf->Output("Tax Invoice-".$id."-".date('Y-m-d').".pdf","D");
        return ;
    }
}