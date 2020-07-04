<?php namespace App\Controllers;


use Config\Database;
use Config\Services;
use Mpdf\Mpdf;

class Receipts extends BaseController{
	public function index(){
		$db=Database::connect();
		$rcpts=$db->table('receipt')->get()->getResult('object');
		return view('content/receipts',['receipts'=>$rcpts,'title'=>'Receipts']);
	}

	public function create(){
		return view('content/receipts_create',['title'=>'Create Receipt']);
	}

	public function save(){
		$session=Services::session();
		$receipt=array(
			'refNo'=>$this->request->getVar('refNo'),
			'accountName'=>$this->request->getVar('accountName'),
			'narration'=>$this->request->getVar('narration'),
			'date'=>$this->request->getVar('date'),
			'refDate'=>$this->request->getVar('refDate'),
			'receivedFrom'=>$this->request->getVar('receivedFrom'),
			'description'=>$this->request->getVar('description'),
			'currency'=>$this->request->getVar('currency'),
			'receivedBy'=>$this->request->getVar('receivedBy'),
			'preparedBy'=>\Config\Services::session()->get('fullName')
		);
		$db=Database::connect();
		$db->table('receipt')->insert($receipt);
		if($db->affectedRows()==1){
			$id=$db->insertID();
			$session->setFlashdata('success','data receipt created');
			return redirect()->to(base_url('receipts/receipt_items/'.$id));
		}else{
			echo 'error';
		}
	}

	public function receipt_items($id){
	    $db=Database::connect();
        $stock=$db->table('inventory')->get()->getResult('object');
        $units=$db->table('units')->get()->getResult('object');
        $items=$db->table('receiptitems')
            ->select('receiptitems.inventoryItem,receiptitems.quantity,receiptitems.unitCost,receiptitems.total,receiptitems.id,receiptitems.units')
            ->getWhere(['receiptId'=>$id])->getResult('object');
        $data['payments']=$db->table('receiptdevices')->getWhere(['receiptId'=>$id])->getResult('object');
        $data['items']=$items;
        $data['units']=$units;
        $data['stock']=$stock;
	    $data['title']='Receipt Items for Receipt Number ('.$id.')';
	    $data['receiptNo']=$id;
        return view('content/receipt_items',$data);
    }

    public function receipt_items2($id){
        $db=Database::connect();
        $stock=$db->table('inventory')->get()->getResult('object');
        $units=$db->table('units')->get()->getResult('object');
        $items=$db->table('receiptitems')
            ->select('receiptitems.inventoryItem,receiptitems.quantity,receiptitems.unitCost,receiptitems.total,receiptitems.id,receiptitems.units')
            ->getWhere(['receiptId'=>$id])->getResult('object');
        $data['receipt']=$db->table('receipt')->getWhere(['receiptId'=>$id])->getResult('object')[0];
        $data['items']=$items;
        $data['units']=$units;
        $data['stock']=$stock;
        $data['title']='Receipt Items for Receipt Number ('.$id.')';
        $data['receiptNo']=$id;
        return view('content/receipt_items_editor',$data);
    }

    public function update_edits($id){
        $receipt=array(
            'refNo'=>$this->request->getVar('refNo'),
            'accountName'=>$this->request->getVar('accountName'),
            'narration'=>$this->request->getVar('narration'),
            'date'=>$this->request->getVar('date'),
            'refDate'=>$this->request->getVar('refDate'),
            'receivedFrom'=>$this->request->getVar('receivedFrom'),
            'description'=>$this->request->getVar('description'),
            'currency'=>$this->request->getVar('currency'),
            'receivedBy'=>$this->request->getVar('receivedBy'),
            'preparedBy'=>\Config\Services::session()->get('fullName')
        );
        $db=Database::connect();
        $db->table('receipt')->update($receipt,['receiptId'=>$id]);
        $data['amount']=$this->request->getVar('amount');
        Services::session()->set('amount',$this->request->getVar('amount'));
        return redirect()->to(base_url('receipts/generate/'.$id));
    }

    public function receipt_items_save($id){
	    $db=Database::connect();
        $unit=$this->request->getVar('units');
        $db->query('INSERT IGNORE INTO UNITS VALUES ("' . $unit . '")');
        $invoice_item=array(
            'inventoryItem'=>$this->request->getVar('inventoryItem'),
            'unitCost'=>$this->request->getVar('unitPrice'),
            'receiptId'=>$this->request->getVar('receiptId'),
            'quantity'=>$this->request->getVar('quantity'),
            'total'=>$this->request->getVar('unitPrice') * $this->request->getVar('quantity'),
            'units'=>$unit
        );
        $db->table('receiptitems')->insert($invoice_item);
        return redirect()->to(base_url('receipts/receipt_items/'.$id));
    }

    public function delete_receipt_item($id,$receiptId){
        $db=Database::connect();
        $db->table('receiptitems')->delete(['id'=>$id]);
        return redirect()->to(base_url('receipts/receipt_items/' . $receiptId));
    }

    public function receipt_items_save_edit($id){
        $db=Database::connect();
        $item_id=$this->request->getVar('itemId');
        $unit=$this->request->getVar('units');
        $db->query('INSERT IGNORE INTO UNITS VALUES ("' . $unit . '")');
        $invoice_item=array(
            'inventoryItem'=>$this->request->getVar('inventoryItem'),
            'unitCost'=>$this->request->getVar('unitPrice'),
            'receiptId'=>$id,
            'quantity'=>$this->request->getVar('quantity'),
            'total'=>$this->request->getVar('unitPrice') * $this->request->getVar('quantity'),
            'units'=>$this->request->getVar('units')
        );

        $db->table('receiptitems')->update($invoice_item, ['id'=>$item_id]);
        //return print_r($db->affectedRows());
        return redirect()->to(base_url('receipts/receipt_items/' . $id));
    }

    public function fetch_invoice_item(){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');$id=$this->request->getVar('id');
        $db=Database::connect();
        $item=$db->table('receiptitems')->getWhere(['id'=>$id])->getResultArray()[0];
        $data['success']=1;
        $data['data']=$item;
        return json_encode($data);
    }

	public function view_receipt($id){
		$db=Database::connect();
		$receipt=$db->table('receipt')->getWhere(['receiptId'=>$id])->getResult('object');
		if (empty($receipt)){
			return redirect()->to(base_url('receipts/create'));
		}
		return view('content/view_receipt',['title'=>'View Receipt','receipt'=>$receipt[0]]);
	}

	public function generate($id){
        if ($this->request->getVar('amount')==null){
            $data['amount']=Services::session()->get('amount');
        }else{
            $data['amount']=$this->request->getVar('amount');
        }
		$db=Database::connect();
		$amountDue=Services::session()->get('amountDue');
		if($amountDue>$data['amount']){
		    $balance=$amountDue-$data['amount'];
		    $record=array(
		        'receiptId'=>$id,
                'amountDue'=>$amountDue,
                'amountPaid'=>$data['amount'],
                'balance'=>$balance,
                'date'=>date('Y-m-d H:i:s')
            );
		    $db->table('receiptdevices')->insert($record);
        }
		$data['receipt']=$db->table('receipt')->getWhere(['receiptId'=>$id])->getResult('object')[0];
		$data['items']=$db->table('receiptitems')->getWhere(['receiptId'=>$id])->getResult('array');
		$data['receiptId']=$id;

		$pdf=new Mpdf();
		$pdf->SetHTMLHeader('<div style="border-bottom: 1px solid #000000;">TAX RECEIPT</div>','E');
		$pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif;font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
    								
    									<tr>
											<td width="33%">{DATE j-m-Y}</td>
											<td width="33%" align="center">"Customer Satisfaction First"</td>
											<td width="33%" align="center">Page: {PAGENO}/{nbpg}</td>
    									</tr>
									</table>');  // Note that the second parameter is optional : default = 'O' for ODD



		$pdf->WriteHTML(view('receipt',$data));
		$pdf->Output("Receipt.pdf","D");
		return ;
	}
	public function delete_receipt($id){
		$db=Database::connect();
		$db->table('receipt')->delete(['receiptId'=>$id]);
		return redirect()->to(base_url('receipts'));
	}
}
