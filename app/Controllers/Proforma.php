<?php
namespace App\Controllers;

use CodeIgniter\Session\Session;
use Config\Database;
use Config\Services;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Mpdf\Mpdf;

class Proforma extends BaseController {
    public function proformaAdjuster32(){
        $db=Database::connect();
        $proforma=$db->table('proforma')->get()->getResultObject();
        foreach($proforma as $i){
            $customer=$db->table('customers')->getWhere(['id'=>$i->customerId])->getResultObject();
            $proforma_update=array(
                'customerName'=>$customer[0]->customerName,
                'contactPerson'=>$customer[0]->contactPerson,
                'tinNo'=>$customer[0]->tinNo,
                'address'=>$customer[0]->address,
                'areaCountry'=>$customer[0]->areaCountry,
                'phone'=>$customer[0]->phone,
                'email'=>$customer[0]->email,
                'otherContactDetails'=>$customer[0]->otherContactDetails,
            );
            $db->table('proforma')->update($proforma_update,['invoiceId'=>$i->invoiceId]);
        }
    }

    public function index(){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $invoices=$db->table('proforma')->
            select('*')->orderBy('invoiceId', 'DESC')->get()->getResult('object');
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
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/proforma.log', Logger::INFO));
        $db=Database::connect();
        //$db->table('proformainvoicenumbers')->insert(['status'=>1]);
        //$proformaId=$db->insertID();
        $invoice=array(
            'customerName'=>$this->request->getVar('customerName'),
            'contactPerson'=>$this->request->getVar('contactPerson'),
            'tinNo'=>$this->request->getVar('tinNo'),
            'address'=>$this->request->getVar('address'),
            'areaCountry'=>$this->request->getVar('areaCountry'),
            'phone'=>$this->request->getVar('phone'),
            'email'=>$this->request->getVar('email'),
            'otherContactDetails'=>$this->request->getVar('otherContactDetails'),
            //'invoiceId'=>$proformaId,
            'customerId'=>$this->request->getVar('customerId'),
            'date'=>$this->request->getVar('date'),
            'currency'=>strtoupper($this->request->getVar('currency')),
            'modeOfPayment'=>$this->request->getVar('modeOfPayment'),
            'lpoNo'=>$this->request->getVar('lpo'),
            'carRegNo'=>strtoupper($this->request->getVar('carRegNo')),
            'carType'=>$this->request->getVar('carType'),
            'mileage'=>$this->request->getVar('mileage'),
            'preparedBy'=>\Config\Services::session()->get('id'),
            //'proformaId'=>$invoiceId,
            'narration'=>$this->request->getVar('narration')
        );
        $db->table('proforma')->insert($invoice);
        $invoiceId=$db->insertID();
        $finance=array(
            'date'=>$this->request->getVar('date'),
            'proformaNo'=>$invoiceId,
            'lpoNo'=>$this->request->getVar('lpo'),
            'customerId'=>$this->request->getVar('customerId'),
            'customerName'=>$this->request->getVar('customerName'),
            'contactPerson'=>$this->request->getVar('contactPerson'),
            'tinNo'=>$this->request->getVar('tinNo'),
            'address'=>$this->request->getVar('address'),
            'areaCountry'=>$this->request->getVar('areaCountry'),
            'phone'=>$this->request->getVar('phone'),
            'email'=>$this->request->getVar('email'),
            'confirmed'=>0,
            'withholdingTax'=>0,
            'vat'=>0,
            'totalpayable'=>0,
            'cleared'=>0,
            'carRegNo'=>strtoupper($this->request->getVar('carRegNo')),
        );
        $db->table('finance')->insert($finance);
        $financeId=$db->insertID();
        if($db->affectedRows()==1){
            $logger->info("New proforma ($invoiceId) created",['maker'=>\session()->get('fullName')]);
            //session()->setFlashdata('success',"New proforma ($invoiceId) created");
            return redirect()->to(base_url('proforma/invoice_items/'.$invoiceId.'/'.$financeId));
        }else{
            echo "Input failed";
            return null;
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function save_detail_edits(){
        $db=Database::connect();
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/proforma.log', Logger::INFO));
        $this->request->getVar('custId');
        $invoice=array(
            'contactPerson'=>$this->request->getVar('contactPerson'),
            'customerName'=>$this->request->getVar('customerName'),
            'address'=>$this->request->getVar('address'),
            'areaCountry'=>$this->request->getVar('areaCountry'),
            'phone'=>$this->request->getVar('phone'),
            'email'=>$this->request->getVar('email'),
            'tinNo'=>$this->request->getVar('tinNo'),
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
        //updating the finance table as well
        $finance=array(
            'contactPerson'=>$this->request->getVar('contactPerson'),
            'customerName'=>$this->request->getVar('customerName'),
            'address'=>$this->request->getVar('address'),
            'areaCountry'=>$this->request->getVar('areaCountry'),
            'phone'=>$this->request->getVar('phone'),
            'email'=>$this->request->getVar('email'),
            'tinNo'=>$this->request->getVar('tinNo'),
            'date'=>$this->request->getVar('date'),
            'lpoNo'=>$this->request->getVar('lpo'),
            'carRegNo'=>strtoupper($this->request->getVar('carRegNo')),
        );
        //return print_r($this->request->getVar('invoice_no'));
        //db->table('customers')->update($customer,['id'=>$custId]);
        $proforma_id=$this->request->getVar('invoice_no');
        $customerName=$this->request->getVar('customerName');
        $db->table('proforma')->update($invoice,['invoiceId'=>$this->request->getVar('invoice_no')]);
        $db->table('finance')->update($finance,['proformaNo'=>$this->request->getVar('invoice_no')]);
        $logger->info("Proforma ($proforma_id) of $customerName has been edited",['maker'=>session()->get('fullName')]);
        session()->setFlashdata('success',"Proforma ($proforma_id)-> $customerName has been edited successfully");
        return redirect()->to(base_url('proforma'));

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
            //$customers=$db->table('customers')->getWhere(['deleted'=>0])->getResult('object');
            //$stock=$db->table('inventory')->get()->getResult('object');
            if (count($row->getResultArray()) != 1) {
                return view('error', ['title'=>"Error", 'message'=>"Sorry, We couldn't find the invoice Id"]);
            }
            $customerId=$row->getResult()[0]->customerId;
            //$customerData=$db->table('proforma')->getWhere(['id'=>$customerId])->getResult();
            //$data['customerData']=$customerData[0];
            $data['invoice_no']=$id;
            $data['invoice']=$row->getResult('object')[0];
            //$data['customers']=$customers;
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

    public function tax_and_discounts($id=0){
        $db=Database::connect();
        $maker=$db->table('proforma')->select('users.fullName as maker')
                    ->join('users','proforma.preparedBy=users.id')
                    ->getWhere(['proforma.invoiceId'=>$id])->getResult('object')[0];
        $items=$db->table('proformaitems2')->select('*')
            ->getWhere(['invoiceId'=>$id])->getResultArray();
        //calculating stuff for finance
        $total=0;
        foreach($items as $item){
            $total=$total+$item['total'];
        }
        $vat=($total*18)/100;
        $gtotal=$total+$vat;
        if($gtotal>=1000000){
            $wtax=(6*$gtotal)/100;
        }else{
            $wtax=0;
        }
        $finance=array(
            'totalPayable'=>$gtotal,
            'vat'=>$vat,
            'withholdingTax'=>$wtax
        );
        $db->table('finance')->update($finance,['proformaNo'=>$id]); //updating the finance table
        $discount=$db->table('proformadiscounts')->getWhere(['invoiceId'=>$id])->getResultArray();
        $custData=$db->table('proforma')
            ->select('*')
            ->getWhere(['invoiceId'=>$id])->getResult('object')[0];
        $date=date('Y-m-d',strtotime($custData->date));
        $before=$db->query("SELECT INVOICEID, DATE, CUSTOMERNAME FROM PROFORMA WHERE DATE<'$date' ORDER BY INVOICEID ASC")
            ->getResult();
        $after=$db->query("SELECT INVOICEID, DATE, CUSTOMERNAME FROM PROFORMA WHERE DATE>'$date' ORDER BY INVOICEID DESC")
            ->getResult();
        //print_r($before);return;
        if(empty($items)){
            return redirect()->to(base_url('proforma/invoice_items/'.$id));
        }
        return view('proforma/taxAndDiscounts',['before'=>$before,'after'=>$after,'maker'=>$maker,'invoiceId'=>$id,'items'=>$items,'data'=>$custData,'title'=>'Tax and Discounts','discount'=>$discount]);
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

    /*public function generate($id){
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
    }*/

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
        select('*')
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

        $pdf->WriteHTML(view('html_convert_pdf_proforma',$data));
        $pdf->Output("Proforma Invoice-".$id."-".date('Y-m-d').".pdf","D");
        return ;
    }

    public function delete_invoice($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $db->query('SET FOREIGN_KEY_CHECKS=0');
            //$db->table('proformaitems2')->delete(['invoiceId'=>$id]);
            //$db->table('proforma')->delete(['invoiceId'=>$id]);
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
