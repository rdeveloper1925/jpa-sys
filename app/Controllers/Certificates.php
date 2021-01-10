<?php
namespace App\Controllers;

use Config\Database;
use Config\Services;
use Mpdf\Mpdf;

class Certificates extends BaseController{
    public function index(){
            $db=Database::connect();
            $certificates=$db->table('certificates')->orderBy('id', 'ASC')->get()->getResult('object');
            return view('certificates/index', ['title'=>'Work Done Completion Certificates', 'certificates'=>$certificates]);
    }

    public function new(){
        $db=Database::connect();
        $customers=$db->table('customers')->get()->getResultObject();
        return view('certificates/new',['title'=>"New Completion Certificate",'customers'=>$customers]);
    }

    public function edit($id){
        $db=Database::connect();
        $customers=$db->table('customers')->get()->getResultObject();
        $certificate=$db->table('certificates')->where('id',$id)->get()->getResultObject()[0];
        return view('certificates/edit',['title'=>"Edit Completion Certificate",'customers'=>$customers,'certificate'=>$certificate]);
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
            'country'=>$this->request->getVar('areaCountry'),
            'email'=>$this->request->getVar('email'),
            'tinNo'=>$this->request->getVar('tinNo')
        );
        $db=Database::connect();
        $db->table('certificates')->insert($certificate);
        $id=$db->insertID();
        //return print_r($certificate);
        return redirect()->to(base_url('certificates/view/'.$id));
    }

    public function update(){
        $id=$this->request->getVar('id');
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
            'country'=>$this->request->getVar('areaCountry'),
            'email'=>$this->request->getVar('email'),
            'tinNo'=>$this->request->getVar('tinNo')
        );
        $db=Database::connect();
        $db->table('certificates')->update($certificate,['id'=>$id]);
        return redirect()->to(base_url('certificates/'))->with('success',"Certificate for ".$this->request->getVar('customerName')." Edited Successfully");
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
        $ttl="WORK DONE COMPLETION CERTIFICATE";
        $pdf=new Mpdf();
        $pdf->SetMargins(15,15,15,15);
        $pdf->SetWatermarkImage(base_url("assets/img/logo.png"),0.3,'F','F');
        $pdf->SetHTMLHeader('<div style="border-bottom: 1px solid #000000;">WORK COMPLETION CERTIFICATE</div>','E');
        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
    								
    									<tr>
											<td width="33%">'.date("d-M-Y",time()).'</td>
											<td width="33%" align="center">"Customer Satisfaction First"</td>
											<td width="33%" align="center">Page: {PAGENO}/{nbpg}</td>
    									</tr>
									</table>');  // Note that the second parameter is optional : default = 'O' for ODD

        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
										<tr>
											<td width="33%"><span style="font-weight: bold; font-style: italic;">My document</span></td>
											<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
											<td width="33%" style="text-align: right; ">'.date("d-M-Y",time()).'</td>
										</tr>
									</table>', 'E');

        //print_r($certificate);return;
        $pdf->WriteHTML(view('certificates/cert_pdf.php',['data'=>$certificate,'ttl'=>$ttl]));
        $pdf->Output("Work Done Certificate-".$id."-".date('Y-m-d').".pdf","D");
        return ;
    }

}