<?php
namespace App\Controllers;

use CodeIgniter\Session\Session;
use Config\Database;
use Config\Services;
use Mpdf\Mpdf;

class Invoices extends BaseController {
	public function index(){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $invoices=$db->table('invoice')->
            select('*')->join('customers', 'invoice.customerId=customers.id', 'inner')
                ->orderBy('date', 'DESC')->get()->getResult('object');
            return view('content/invoices', ['title'=>'Tax Invoices', 'invoices'=>$invoices]);
        }
        return redirect()->to(base_url('pages/error'));
	}

    public function custdetails_edit($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $row=$db->table('invoice')->getWhere(['invoiceId'=>$id]);
            if (count($row->getResultArray()) != 1) {
                return view('error', ['title'=>"Error", 'message'=>"Sorry, We couldn't find the invoice Id"]);
            }
            $customerId=$row->getResult()[0]->customerId;
            $customerData=$db->table('customers')->getWhere(['id'=>$customerId])->getResult();
            $data['customerData']=$customerData[0];
            $data['invoice_no']=$id;
            $data['invoice']=$row->getResult('object')[0];
            $data['title']="Edit Invoice Details. (" . $id . ")";
            return view('content/edit-invoice-details', $data);
        }
        return redirect()->to(base_url('pages/error'));
    }

	public function create(){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $customers=$db->table('customers')->getWhere(['deleted'=>0])->getResult('object');
            return view('content/create-invoice', ['title'=>'Tax Invoices - Create', 'customers'=>$customers]);
        }
        return redirect()->to(base_url('pages/error'));
	}

	public function save(){
		//alter table invoice AUTO_INCREMENT=21324
        //return print_r($this->request->getVar('existingData'));
        $db=Database::connect();
        $db->table('proformainvoicenumbers')->insert(['status'=>1]);
        $proformaId=$db->insertID();
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
		$db->table('invoice')->insert($invoice);
		$invoiceId=$db->insertID();
		//return print_r($invoiceId);
		if($this->request->getVar('existingData')=='existing'){
		    return redirect()->to(base_url('invoices/existing/'.$invoiceId));
		}else{
		    return redirect()->to(base_url('invoices/invoice_items/'.$invoiceId));
		}
	}

	public function existing($invoiceId){
        $data['title']="Use Exising Proforma Items";
        $data['taxId']=$invoiceId;
        return view('content/existing',$data);
    }

    public function search($invoiceId){
        $data['title']="Use Exising Proforma Items";
        $data['taxId']=$invoiceId;
        $db=Database::connect();
        $stock=$db->table('inventory')->get()->getResult('object');
        $usr=\Config\Services::session()->get('id');
        $id=$this->request->getVar('invoiceNo');
        $proforma=$db->table('proforma')->getWhere(['invoiceId'=>$id])->getResult();
        $items=$db->table('proformaitems2')->getWhere(['invoiceId'=>$id])->getResult();
        $db->table('invoiceitemstemp')->delete(['userId'=>$usr]);
        if (!empty($proforma)&&!empty($items)){
            foreach ($items as $i){
                $invoice_item=array(
                    'inventoryItem'=>$i->inventoryItem,
                    'unitCost'=>$i->unitCost,
                    'invoiceId'=>$i->invoiceId,
                    'quantity'=>$i->quantity,
                    'total'=>$i->unitCost * $i->quantity,
                    'units'=>$i->units,
                    'userId'=>$usr
                );
                $db->table('invoiceitemstemp')->insert($invoice_item);
            }
            $data['proforma']=$proforma[0];
            $data['stock']=$stock;
            $data['items']=$db->table('invoiceitemstemp')->getWhere(['userId'=>$usr])->getResult();
            return view('content/existing',$data);
        }else{
            echo "Proforma not found. Please check and try again";
            return;
        }
    }

    public function delete_temp($itemid,$taxId,$proformaId){
	    $db=Database::connect();
        $usr=\Config\Services::session()->get('id');
	    $db->table('invoiceitemstemp')->delete(['id'=>$itemid,'userId'=>$usr]);
        $data['title']="Use Exising Proforma Items";
        $data['taxId']=$taxId;
        $stock=$db->table('inventory')->get()->getResult('object');
        $data['stock']=$stock;
        $proforma=$db->table('proforma')->getWhere(['invoiceId'=>$proformaId])->getResult();
        $data['proforma']=$proforma[0];
        $data['items']=$db->table('invoiceitemstemp')->getWhere(['userId'=>$usr])->getResult();
        return view('content/existing',$data);
    }

    public function new_temp_item($proformaId){
        $db=Database::connect();
        $usr=\Config\Services::session()->get('id');
        $stock=$db->table('inventory')->get()->getResult('object');
        $invoice_item=array(
            'inventoryItem'=>$this->request->getVar('inventoryItem'),
            'unitCost'=>$this->request->getVar('unitPrice'),
            'invoiceId'=>$this->request->getVar('taxId'),
            'quantity'=>$this->request->getVar('quantity'),
            'total'=>$this->request->getVar('unitPrice') * $this->request->getVar('quantity'),
            'units'=>$this->request->getVar('units'),
            'userId'=>$usr
        );
        $db->table('invoiceitemstemp')->insert($invoice_item);
        $data['stock']=$stock;
        $data['title']="Use Exising Proforma Items";
        $data['taxId']=$this->request->getVar('taxId');
        $proforma=$db->table('proforma')->getWhere(['invoiceId'=>$proformaId])->getResult();
        $data['proforma']=$proforma[0];
        $data['items']=$db->table('invoiceitemstemp')->getWhere(['userId'=>$usr])->getResult();
        return view('content/existing',$data);
    }

    public function fetch_proforma_items(){
	    $db=Database::connect();
	    $id=$this->request->getVar('invoiceId');
	    $proforma=$db->table('proforma')->getWhere(['invoiceId'=>$id])->getResult();
	    $items=$db->table('proformaitems2')->getWhere(['invoiceId'=>$id])->getResult();
	    if (!empty($proforma)&&!empty($items)){
	        $data['proforma']=$proforma[0];
	        $data['items']=$items;
	        $data['success']=1;
	        return json_encode($data);
        }else{
	        return json_encode(['success'=>0]);
        }

    }

    public function confirm(){
	    $id=$this->request->getVar('invoiceId');
        $usr=\Config\Services::session()->get('id');
	    $db=Database::connect();
        $items=$db->table('invoiceitemstemp')->getWhere(['userId'=>$usr])->getResult('array');
        foreach ($items as $i){
            $data=array(
                'inventoryItem'=>$i['inventoryItem'],
                'quantity'=>$i['quantity'],
                'unitCost'=>$i['unitCost'],
                'invoiceId'=>$this->request->getVar('taxId'),
                'units'=>$i['units'],
                'total'=>$i['total']
            );
            $db->table('invoiceitems2')->insert($data);

        }
        return redirect()->to(base_url('invoices/tax_and_discounts/'.$this->request->getVar('taxId')));
    }

    public function save_edits(){
        $db=Database::connect();
        $custId=$this->request->getVar('custId');
        $customer=array(
            'contactPerson'=>$this->request->getVar('contactPerson'),
            'customerName'=>$this->request->getVar('customerName'),
            'address'=>$this->request->getVar('address'),
            'areaCountry'=>$this->request->getVar('areaCountry'),
            'phone'=>$this->request->getVar('phone'),
            'email'=>$this->request->getVar('email'),
            'tinNo'=>$this->request->getVar('tinNo')
        );
        $invoice=array(
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
        //return print_r($invoice);
        $db->table('customers')->update($customer,['id'=>$custId]);
        $db->table('invoice')->update($invoice,['invoiceId'=>$this->request->getVar('invoiceId')]);
        //if($db->affectedRows()==1){
            //$invoiceId=$db->insertID();
            return redirect()->to(base_url('invoices'));
        //}else{
            echo "Input failed";
            return null;
        //}
        return redirect()->to(base_url('pages/error'));
    }

	public function invoice_items($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $row=$db->table('invoice')->getWhere(['invoiceId'=>$id]);
            $stock=$db->table('inventory')->get()->getResult('object');
            $units=$db->table('units')->select('distinct(unit) as unit')->get()->getResult('object');
            if (count($row->getResultArray()) != 1) {
                return view('error', ['title'=>"Error", 'message'=>"Sorry, We couldn't find the invoice Id"]);
            }
            $tempItems=$db->table('inventoryTemp')->select('id,itemName as partName')->get()->getResult('object');
            foreach ($tempItems as $temp) {
                array_push($stock, $temp);
            }
            $items=$db->table('invoiceitems2')
                ->select('invoiceitems2.inventoryItem,invoiceitems2.quantity,invoiceitems2.unitCost,invoiceitems2.total,invoiceitems2.id,invoiceitems2.units')
                ->getWhere(['invoiceId'=>$id])->getResult('object');
            $data['invoice_no']=$id;
            $data['items']=$items;
            $data['stock']=$stock;
            $data['units']=$units;
            $data['title']="Invoice items (Invoice No." . $id . ")";
            return view('content/invoice_items', $data);
        }
        return redirect()->to(base_url('pages/error'));
	}

	public function invoice_items_edit($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $row=$db->table('invoice')->getWhere(['invoiceId'=>$id]);
            $stock=$db->table('inventory')->get()->getResult('object');
            if (count($row->getResultArray()) != 1) {
                return view('error', ['title'=>"Error", 'message'=>"Sorry, We couldn't find the invoice Id"]);
            }
            $items=$db->table('invoiceitems2')
                ->select('*')->getWhere(['invoiceId'=>$id])->getResult('object');
            $units=$db->table('units')->get()->getResult('object');
            $data['units']=$units;
            $data['invoice_no']=$id;
            $data['items']=$items;
            $data['stock']=$stock;
            $data['title']="Invoice items (Invoice No." . $id . ")";
            return view('content/invoice_items', $data);
        }
        return redirect()->to(base_url('pages/error'));
    }

	public function invoice_items_save($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $row=$db->table('invoice')->getWhere(['invoiceId'=>$id]);
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
            $db->table('invoiceitems2')->insert($invoice_item);
            if ($db->affectedRows() == 1) {
                return redirect()->to(base_url('invoices/invoice_items/' . $id));
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
            $db->table('invoiceitems2')->delete(['id'=>$id]);
            return redirect()->to(base_url('invoices/invoice_items/' . $invoiceId));
        }
        return redirect()->to(base_url('pages/error'));
	}

	public function fetch_invoice_item(){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');$id=$this->request->getVar('id');
            $db=Database::connect();
            $item=$db->table('invoiceitems2')->getWhere(['id'=>$id])->getResultArray()[0];
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

            $db->table('invoiceitems2')->update($invoice_item, ['id'=>$item_id]);
            return redirect()->to(base_url('invoices/invoice_items/' . $id));
        }
        return redirect()->to(base_url('pages/error'));
    }

	public function tax_and_discounts($id){
        $db=Database::connect();
        $maker=$db->table('invoice')->select('users.fullName as maker')
                    ->join('users','invoice.preparedBy=users.id')
                    ->getWhere(['invoice.invoiceId'=>$id])->getResult('object')[0];
		$items=$db->table('invoiceitems2')->select('*')
                    ->getWhere(['invoiceId'=>$id])->getResultArray();
		$discount=$db->table('discounts')->getWhere(['invoiceId'=>$id])->getResultArray();
		$custData=$db->table('customers')
                ->select('*')->join('invoice','invoice.customerId=customers.id','inner')
                ->getWhere(['invoiceId'=>$id])->getResult('object')[0];
        $date=date('Y-m-d',strtotime($custData->date));
        $before=$db->query("select invoice.invoiceId,invoice.date,customers.customerName from invoice left JOIN customers on invoice.customerId=customers.id where invoice.date<'$date' group by invoice.date asc ")
            ->getResult();
        $after=$db->query("select invoice.invoiceId,invoice.date,customers.customerName from invoice left JOIN customers on invoice.customerId=customers.id where invoice.date>$date group by invoice.date asc ")
            ->getResult();
		//print_r($items);return;
		if(empty($items)){
			return redirect()->to(base_url('invoices/invoice_items/'.$id));
		}
		return view('content/taxAndDiscounts',['before'=>$before,'after'=>$after,'maker'=>$maker,'invoiceId'=>$id,'items'=>$items,'data'=>$custData,'title'=>'Tax and Discounts','discount'=>$discount]);
	}

	public function apply_discount($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $rs=$db->table('discounts')->getWhere(['invoiceId'=>$id])->getResultArray();
            if (!empty($rs)) {
                return redirect()->to(base_url('invoices/tax_and_discounts/' . $id));
            }
            $db->table('discounts')->insert(
                ['discount'=>$this->request->getVar('discount'), 'invoiceId'=>$id]
            );
            return redirect()->to(base_url('invoices/tax_and_discounts/' . $id));
        }
        return redirect()->to(base_url('pages/error'));
	}

	public function remove_discount($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $db->table('discounts')->delete(['invoiceId'=>$id]);
            return redirect()->to(base_url('invoices/tax_and_discounts/' . $id));
        }
        return redirect()->to(base_url('pages/error'));
	}

	public function generate_old($id){
		$pdf=new Mpdf();
		$pdf->SetHTMLHeader('<div style="text-align: right; font-weight: bold;">INVOICE</div>','O');
		$pdf->SetHTMLHeader('<div style="border-bottom: 1px solid #000000;">INVOICE</div>','E');
		$pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
    									<tr>
											<td width="33%">{DATE j-m-Y}</td>
											<td width="33%" align="center">{PAGENO}/{nbpg}</td>
											<td width="33%" style="text-align: right;">My document</td>
    									</tr>
									</table>');  // Note that the second parameter is optional : default = 'O' for ODD

		$pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
										<tr>
											<td width="33%"><span style="font-weight: bold; font-style: italic;">My document</span></td>
											<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
											<td width="33%" style="text-align: right; ">{DATE j-m-Y}</td>
										</tr>
									</table>', 'E');

		$pdf->WriteHTML(view('html_convert_pdf'));
		$pdf->Output("mine.pdf","D");
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

    public function generate2($id){
        $db=Database::connect();
        $data['ttl']="PROFORMA INVOICE";
        $data['words']=$this->request->getVar('words2');
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


        $pdf->WriteHTML(view('html_convert_pdf',$data));
        $pdf->Output("Invoice-".$id."-".date('Y-m-d').".pdf","D");
        return ;
    }

	public function delete_invoice($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='SUPERVISOR'||$l=='ACCOUNTANT'||$l=='RECEPTIONIST'||$l=='MARKETEER'||$l=='PROCUREMENT') {
            $db=Database::connect();
            $db->query('SET FOREIGN_KEY_CHECKS=0');
            $db->table('invoiceitems2')->delete(['invoiceId'=>$id]);
            $db->table('invoice')->delete(['invoiceId'=>$id]);
            return redirect()->to(base_url('invoices/'));
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
