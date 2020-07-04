<?php
namespace App\Controllers;

use CodeIgniter\Session\Session;
use Config\Database;
use Config\Services;
use Mpdf\Mpdf;

class Proforma extends BaseController {
    public function index(){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $invoices=$db->table('proforma')->
            select('*')->join('customers', 'proforma.customerId=customers.id', 'inner')
                ->orderBy('date', 'DESC')->get()->getResult('object');
            return view('proforma/proformas', ['title'=>'Proforma Invoices', 'invoices'=>$invoices]);
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function create(){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $customers=$db->table('customers')->getWhere(['deleted'=>0])->getResult('object');
            return view('proforma/create-proforma', ['title'=>'Proforma - Create', 'customers'=>$customers]);
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function use_existing(){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $invoiceId=$this->request->getVar('invoice_no');
            $rows=$db->table('invoiceitems2')->getWhere(['invoiceId'=>$invoiceId])->getResult('object');
            if(empty($rows)){
                echo "Sorry the Tax invoice Number can not be found ";
                return null;
            }
            $data['title']='Invoice Items for Tax Invoice: '.$invoiceId;
            $data['rows']=$rows;
            $data['invoiceId']=$invoiceId;
            //$customers=$db->table('customers')->getWhere(['deleted'=>0])->getResult('object');
            return view('proforma/view-items', $data);
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function confirm($id){
        $db=Database::connect();
        $invoiceId=$id;
        $rows=$db->table('invoiceitems2')->getWhere(['invoiceId'=>$invoiceId])->getResult('object');
        if(empty($rows)){
            echo "Sorry the Tax invoice Number can not be found ";
            return null;
        }
        foreach ($rows as $r){
            $item=array(
                'inventoryItem'=>$r->inventoryItem,
                'quantity'=>$r->quantity,
                'unitCost'=>$r->unitCost,
                'invoiceId'=>$invoiceId,
                'units'=>$r->units,
                'total'=>$r->total
            );
            $db->table('proformaitems2')->insert($item);
        }
        return redirect()->to(base_url('proforma/invoice_items/'.$invoiceId));
    }

    public function save(){
        //alter table invoice AUTO_INCREMENT=21324
        //return print_r($this->request->getVar());
        $db=Database::connect();
        $db->table('proformainvoicenumbers')->insert(['status'=>1]);
        $proformaId=$db->insertID();
        $invoice=array(
            'invoiceId'=>$proformaId,
            'customerId'=>$this->request->getVar('customerId'),
            'date'=>$this->request->getVar('date'),
            'currency'=>strtoupper($this->request->getVar('currency')),
            'modeOfPayment'=>$this->request->getVar('modeOfPayment'),
            'lpoNo'=>$this->request->getVar('lpo'),
            'carRegNo'=>strtoupper($this->request->getVar('carRegNo')),
            'carType'=>$this->request->getVar('carType'),
            'mileage'=>$this->request->getVar('mileage'),
            'preparedBy'=>\Config\Services::session()->get('id'),
            'proformaId'=>$proformaId,
            'narration'=>$this->request->getVar('narration')
        );
        $customer=$db->table('customers')->getWhere(['id'=>$this->request->getVar('customerId')])->getResult('object')[0];
        //print_r($customer);
        $custDetails=array(
            'contactPerson'=>$customer->contactPerson,
            'address'=>$customer->address,
            'areaCountry'=>$customer->areaCountry,
            'phone'=>$customer->phone,
            'email'=>$customer->email,
            'tinNo'=>$customer->tinNo
        );
        $db->table('customers')->update($custDetails,['id'=>$this->request->getVar('customerId')]);
        $db->table('proforma')->insert($invoice);
        if($db->affectedRows()==1){
            $invoiceId=$db->insertID();
            return redirect()->to(base_url('proforma/invoice_items/'.$invoiceId));
        }else{
            echo "Input failed";
            return null;
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function save_detail_edits(){
        //alter table invoice AUTO_INCREMENT=21324
        //return print_r($this->request->getVar());
        $db=Database::connect();
        //$db->table('proformainvoicenumbers')->insert(['status'=>1]);
        //$proformaId=$db->insertID();
        $invoice=array(
            'customerId'=>$this->request->getVar('customerId'),
            'date'=>$this->request->getVar('date'),
            'currency'=>strtoupper($this->request->getVar('currency')),
            'modeOfPayment'=>$this->request->getVar('modeOfPayment'),
            'lpoNo'=>$this->request->getVar('lpo'),
            'carRegNo'=>strtoupper($this->request->getVar('carRegNo')),
            'carType'=>$this->request->getVar('carType'),
            'mileage'=>$this->request->getVar('mileage'),
            'preparedBy'=>\Config\Services::session()->get('id'),
            'narration'=>$this->request->getVar('narration')
        );
        $customer=$db->table('customers')->getWhere(['id'=>$this->request->getVar('customerId')])->getResult('object')[0];
        //print_r($customer);
        $custDetails=array(
            'contactPerson'=>$customer->contactPerson,
            'address'=>$customer->address,
            'areaCountry'=>$customer->areaCountry,
            'phone'=>$customer->phone,
            'email'=>$customer->email,
            'tinNo'=>$customer->tinNo
        );
        $db->table('customers')->update($custDetails,['id'=>$this->request->getVar('customerId')]);
        $db->table('proforma')->update($invoice,['invoiceId'=>$this->request->getVar('invoice_no')]);
        if($db->affectedRows()==1){
            //$invoiceId=$db->insertID();
            return redirect()->to(base_url('proforma'));
        }else{
            echo "Input failed";
            return null;
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function invoice_items($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $row=$db->table('proforma')->getWhere(['invoiceId'=>$id]);
            $stock=$db->table('inventory')->get()->getResult('object');
            $units=$db->table('units')->select('distinct(unit) as unit')->get()->getResult('object');
            if (count($row->getResultArray()) != 1) {
                return view('error', ['title'=>"Error", 'message'=>"Sorry, We couldn't find the invoice Id"]);
            }
            $tempItems=$db->table('inventoryTemp')->select('id,itemName as partName')->get()->getResult('object');
            foreach ($tempItems as $temp) {
                array_push($stock, $temp);
            }
            $items=$db->table('proformaitems2')
                ->select('proformaitems2.inventoryItem,proformaitems2.quantity,proformaitems2.unitCost,proformaitems2.total,proformaitems2.id,proformaitems2.units')
                ->getWhere(['invoiceId'=>$id])->getResult('object');
            $data['invoice_no']=$id;
            $data['items']=$items;
            $data['stock']=$stock;
            $data['units']=$units;
            $data['title']="Proforma items (Proforma No." . $id . ")";
            return view('proforma/proforma_items', $data);
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function custdetails_edit($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $row=$db->table('proforma')->getWhere(['invoiceId'=>$id]);
            $customers=$db->table('customers')->getWhere(['deleted'=>0])->getResult('object');
            //$stock=$db->table('inventory')->get()->getResult('object');
            if (count($row->getResultArray()) != 1) {
                return view('error', ['title'=>"Error", 'message'=>"Sorry, We couldn't find the invoice Id"]);
            }
            /*$items=$db->table('proformaitems2')
                ->select('*')->getWhere(['invoiceId'=>$id])->getResult('object');
            $units=$db->table('units')->get()->getResult('object');
            $data['units']=$units;*/
            $data['invoice_no']=$id;
            $data['invoice']=$row->getResult('object')[0];
            $data['customers']=$customers;
            $data['title']="Edit Proforma Details No. (" . $id . ")";
            return view('proforma/edit-proforma-details', $data);
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function invoice_items_edit($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $row=$db->table('proforma')->getWhere(['invoiceId'=>$id]);
            $stock=$db->table('inventory')->get()->getResult('object');
            if (count($row->getResultArray()) != 1) {
                return view('error', ['title'=>"Error", 'message'=>"Sorry, We couldn't find the invoice Id"]);
            }
            $items=$db->table('proformaitems2')
                ->select('*')->getWhere(['invoiceId'=>$id])->getResult('object');
            $units=$db->table('units')->get()->getResult('object');
            $data['units']=$units;
            $data['invoice_no']=$id;
            $data['items']=$items;
            $data['stock']=$stock;
            $data['title']="Invoice items (Invoice No." . $id . ")";
            return view('proforma/proforma_items', $data);
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function invoice_items_save($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $row=$db->table('proforma')->getWhere(['invoiceId'=>$id]);
            if (count($row->getResultArray()) != 1) {
                return view('error', ['title'=>"Error", 'message'=>"Sorry, We couldn't find the invoice Id"]);
            }
            $unit=$this->request->getVar('units');
            $db->query('INSERT IGNORE INTO UNITS VALUES ("' . $unit . '")');
            $tester=$db->table('inventorytemp')->getWhere(['itemName'=>$this->request->getVar('inventoryItem')])->getResultArray();
            if(empty($tester)){
                $insertion=array(
                    'itemName'=>$this->request->getVar('inventoryItem'),
                    'inputBy'=>'TEMP'
                );
                $db->table('inventorytemp')->insert($insertion);
            }
            $invoice_item=array(
                'inventoryItem'=>$this->request->getVar('inventoryItem'),
                'unitCost'=>$this->request->getVar('unitPrice'),
                'invoiceId'=>$this->request->getVar('invoiceId'),
                'quantity'=>$this->request->getVar('quantity'),
                'total'=>$this->request->getVar('unitPrice') * $this->request->getVar('quantity'),
                'units'=>$unit
            );
            //return print_r($invoice_item);
            $db->table('proformaitems2')->insert($invoice_item);
            if ($db->affectedRows() == 1) {
                return redirect()->to(base_url('proforma/invoice_items/' . $id));
            }
            return view('error', ['title'=>"Error", 'message'=>"Sorry, An error occured Retry"]);
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function delete_invoice_item($id,$invoiceId){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $db->table('proformaitems2')->delete(['id'=>$id]);
            return redirect()->to(base_url('proforma/invoice_items/' . $invoiceId));
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function fetch_invoice_item(){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');$id=$this->request->getVar('id');
        $db=Database::connect();
        $item=$db->table('proformaitems2')->getWhere(['id'=>$id])->getResultArray()[0];
        $data['success']=1;
        $data['data']=$item;
        return json_encode($data);
    }

    public function invoice_items_save_edit($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $item_id=$this->request->getVar('itemId');
            $unit=$this->request->getVar('units');
            $db->query('INSERT IGNORE INTO UNITS VALUES ("' . $unit . '")');
            $invoice_item=array(
                'inventoryItem'=>$this->request->getVar('inventoryItem'),
                'unitCost'=>$this->request->getVar('unitPrice'),
                'invoiceId'=>$id,
                'quantity'=>$this->request->getVar('quantity'),
                'total'=>$this->request->getVar('unitPrice') * $this->request->getVar('quantity'),
                'units'=>$this->request->getVar('units')
            );

            $db->table('proformaitems2')->update($invoice_item, ['id'=>$item_id]);
            return redirect()->to(base_url('proforma/invoice_items/' . $id));
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function tax_and_discounts($id){
        $db=Database::connect();
        $maker=$db->table('proforma')->select('users.fullName as maker')
                    ->join('users','proforma.preparedBy=users.id')
                    ->getWhere(['proforma.invoiceId'=>$id])->getResult('object')[0];
        $items=$db->table('proformaitems2')->select('*')
            ->getWhere(['invoiceId'=>$id])->getResultArray();
        $discount=$db->table('proformadiscounts')->getWhere(['invoiceId'=>$id])->getResultArray();
        $custData=$db->table('customers')
            ->select('*')->join('proforma','proforma.customerId=customers.id','inner')
            ->getWhere(['invoiceId'=>$id])->getResult('object')[0];
        //print_r($items);return;
        if(empty($items)){
            return redirect()->to(base_url('proforma/invoice_items/'.$id));
        }
        return view('proforma/taxAndDiscounts',['maker'=>$maker,'invoiceId'=>$id,'items'=>$items,'data'=>$custData,'title'=>'Tax and Discounts','discount'=>$discount]);
    }

    public function apply_discount($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $rs=$db->table('proformadiscounts')->getWhere(['invoiceId'=>$id])->getResultArray();
            if (!empty($rs)) {
                return redirect()->to(base_url('proforma/tax_and_discounts/' . $id));
            }
            $db->table('proformadiscounts')->insert(
                ['discount'=>$this->request->getVar('discount'), 'invoiceId'=>$id]
            );
            return redirect()->to(base_url('proforma/tax_and_discounts/' . $id));
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function remove_discount($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $db->table('proformadiscounts')->delete(['invoiceId'=>$id]);
            return redirect()->to(base_url('proforma/tax_and_discounts/' . $id));
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function generate($id){
        $db=Database::connect();
        $data['ttl']="TAX INVOICE";
        $data['words']=$this->request->getVar('words');
        $data['items']=$db->table('invoiceitems')->getWhere(['invoiceId'=>$id])->getResultArray();
        $data['items']=$db->table('proformaitems2')->select('*')
            ->getWhere(['invoiceId'=>$id])->getResultArray();
        $data['discount']=$db->table('discounts')->getWhere(['invoiceId'=>$id])->getResultArray();
        //$data['data']=$db->table('invoice')->getWhere(['invoiceId'=>$id])->getResult('object')[0];
        $data['invoiceId']=$id;
        $data['data']=$db->table('invoice')->
        select('*')->join('customers','invoice.customerId=customers.id','inner')
            ->join('users','invoice.preparedBy=users.id','inner')
            ->orderBy('date','DESC')->getWhere(['invoiceId'=>$id])->getResult('object')[0];

        $pdf=new Mpdf(['setAutoTopMargin' => 'pad']);
        $pdf->SetWatermarkImage(base_url("assets/img/logo.png"),0.3,'F','F');
        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
    								
    									<tr>
											<td width="33%">{DATE j-m-Y}</td>
											<td width="33%" align="center">"Customer Satisfaction First"</td>
											<td width="33%" align="center">Page: {PAGENO}/{nbpg}</td>
    									</tr>
									</table>');  // Note that the second parameter is optional : default = 'O' for ODD

        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
										<tr>
											<td width="33%"><span style="font-weight: bold; font-style: italic;">My document</span></td>
											<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
											<td width="33%" style="text-align: right; ">{DATE j-m-Y}</td>
										</tr>
									</table>', 'E');

        $pdf->WriteHTML(view('html_convert_pdf',$data));
        $pdf->Output("Invoice-".$id."-".date('Y-m-d').".pdf","D");
        return ;
    }

    public function generate2($id){
        $db=Database::connect();
        $data['ttl']="PROFORMA INVOICE";
        $data['words']=$this->request->getVar('words2');
        //$data['items']=$db->table('invoiceitems')->getWhere(['invoiceId'=>$id])->getResultArray();
        $data['items']=$db->table('proformaitems2')->select('*')
            ->getWhere(['invoiceId'=>$id])->getResultArray();
        $data['discount']=$db->table('proformadiscounts')->getWhere(['invoiceId'=>$id])->getResultArray();
        //$data['data']=$db->table('invoice')->getWhere(['invoiceId'=>$id])->getResult('object')[0];
        $data['invoiceId']=$id;
        $data['data']=$db->table('proforma')->
        select('*')->join('customers','proforma.customerId=customers.id','inner')
            ->join('users','proforma.preparedBy=users.id','inner')
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
        $pdf->Output("Proforma Invoice-".$id."-".date('Y-m-d').".pdf","D");
        return ;
    }

    public function delete_invoice($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $db->query('SET FOREIGN_KEY_CHECKS=0');
            $db->table('proformaitems2')->delete(['invoiceId'=>$id]);
            $db->table('proforma')->delete(['invoiceId'=>$id]);
            return redirect()->to(base_url('proforma/'));
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function fetch_customer(){
        $id=$this->request->getVar('id');
        $db=Database::connect();
        $customer=$db->table('customers')->getWhere(['id'=>$id])->getResult('object')[0];
        $data['success']=1;
        $data['customer']=$customer;
        return json_encode($data);
    }

    public function fetch_inventory(){
        $db=Database::connect();
        $id=$this->request->getVar('id');
        $stock_item=$db->table('inventory')->getWhere(['id'=>$id])->getResult('array')[0];
        $data['success']=1;
        $data['stock_item']=$stock_item;
        return json_encode($data);
    }
}
