<?php
namespace App\Controllers;

use CodeIgniter\Session\Session;
use Config\Database;
use Config\Services;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Suppliers extends BaseController {
    public function index(){
        $db=Database::connect();
        $data['suppliers']=$db->table('suppliers')->get()->getResult();
        $data['title']="Suppliers";
        return view('suppliers/suppliers',$data);
    }
    public function save(){
        $db=Database::connect();
        $supplier=array(
            'supplier_name'=>$this->request->getVar('name'),
            'contact'=>$this->request->getVar('supplier_contact'),
            'balance'=>0
        );
        $db->table('suppliers')->insert($supplier);
        return redirect()->to(base_url('suppliers/'));
    }

    public function view_ledger($id){
        $db=Database::connect();
        $supplier=$db->table('suppliers')->getWhere(['id'=>$id])->getResult('object')[0];
        $stock=$db->table('inventory')->get()->getResult('object');
        $result=$db->table('supplier_ledgers')->getWhere(['supplier_id'=>$id])->getResult('object');
        $data['ledger_items']=$result;
        $data['stock']=$stock;
        $data['supplier']=$supplier;
        $data['title']='Supplier Ledger: '.$supplier->supplier_name;
        return view('suppliers/view_ledger',$data);
    }

    public function fetch(){
        $id=$this->request->getVar('id');
        //$id=1;
        $db=Database::connect();
        $supplier=$db->table('suppliers')->getWhere(['id'=>$id])->getResultArray()[0];
        $supplier['success']=1;
        return (json_encode($supplier));
    }

    public function edit(){
        $db=Database::connect();
        $id=$this->request->getVar('id');
        $supplier=array(
            'supplier_name'=>$this->request->getVar('supplier_name'),
            'contact'=>$this->request->getVar('supplier_contact')
        );
        $db->table('suppliers')->update($supplier,['id'=>$id]);
        return redirect()->to(base_url('suppliers/'));
    }

    public function save_supplied_item(){
        $db=Database::connect();
        $supplied_item=array(
            'supply_date'=>$this->request->getVar('date'),
            'item'=>$this->request->getVar('itemSupplied'),
            'invoice_no'=>$this->request->getVar('invoiceNo'),
            'amount'=>$this->request->getVar('quantity')*$this->request->getVar('unitCost'),
            'unitCost'=>$this->request->getVar('unitCost'),
            'cartype'=>$this->request->getVar('carType'),
            'supplier_id'=>$this->request->getVar('supplier_id'),
            'part_no'=>$this->request->getVar('partNo'),
            'debit_note_no'=>$this->request->getVar('debitNoteNo'),
            'quantity'=>$this->request->getVar('quantity'),
        );
        $db->table('supplier_ledgers')->insert($supplied_item);
        return redirect()->to(base_url('suppliers/view_ledger/'.$this->request->getVar('supplier_id')));
    }

    public function edit_item($id){
        $db=Database::connect();
        $item=$db->table('supplier_ledgers')->getWhere(['id'=>$id])->getResult()[0];
        $stock=$db->table('inventory')->get()->getResult('object');
        $data['item']=$item;
        $data['stock']=$stock;
        $data['title']="Edit Ledger Item";
        $data['item_id']=$id;
        return view('suppliers/edit_item',$data);
    }

    public function save_edited_item($id){
        $db=Database::connect();
        $supplierId=$this->request->getVar('supplier_id');
        $supplied_item=array(
            'supply_date'=>$this->request->getVar('date'),
            'item'=>$this->request->getVar('itemSupplied'),
            'invoice_no'=>$this->request->getVar('invoiceNo'),
            'amount'=>$this->request->getVar('quantity')*$this->request->getVar('unitCost'),
            'unitCost'=>$this->request->getVar('unitCost'),
            'cartype'=>$this->request->getVar('carType'),
            'supplier_id'=>$this->request->getVar('supplier_id'),
            'part_no'=>$this->request->getVar('partNo'),
            'debit_note_no'=>$this->request->getVar('debitNoteNo'),
            'quantity'=>$this->request->getVar('quantity'),
            'settled'=>$this->request->getVar('settled')
        );
        $db->table('supplier_ledgers')->update($supplied_item,['id'=>$id]);
        return redirect()->to(base_url('suppliers/view_ledger/'.$supplierId));
    }

    public function cheque_voucher(){
        $db=Database::connect();
        $suppliers=$db->table('suppliers')->get()->getResult();
        $stock=$db->table('inventory')->get()->getResult();
        $data['stock']=$stock;
        $data['suppliers']=$suppliers;
        $data['title']="New cheque voucher";
        return view('suppliers/cheque_voucher',$data);
    }

    public function save_cheque_voucher(){
        $voucher=array(
            'name'=>$this->request->getVar('name'),
            'supplier'=>$this->request->getVar('supplier'),
            'address'=>$this->request->getVar('address'),
            'chequeNo'=>$this->request->getVar('chequeNo'),
            'maker'=>\CodeIgniter\Config\Services::session()->get('fullName'),
            'date'=>$this->request->getVar('date'),
            'passer'=>$this->request->getVar('passer'),
            'authorizer'=>$this->request->getVar('authorizer'),
            'receiver'=>$this->request->getVar('receiver')
        );
        //return print_r($voucher);
        $db=Database::connect();
        $db->table('cheque_voucher')->insert($voucher);
        $voucher['id']=$db->insertID();
        $data['suppliers']=$suppliers=$db->table('suppliers')->get()->getResult();
        $data['title']="Add Voucher items";
        $data['voucher']=$voucher;
        //return print_r($voucher);
        $data['voucherId']=$db->insertID();
        return view('suppliers/cheque_voucher_items',$data);
    }

    public function save_voucher_item(){
        $item=array(
            'particulars'=>$this->request->getVar('particulars'),
            'code'=>$this->request->getVar('code'),
            'amount'=>$this->request->getVar('amount'),
            'voucherId'=>$this->request->getVar('voucherId')
        );
        $db=Database::connect();
        //return print_r($item);
        $db->table('check_voucher_items')->insert($item);
        $voucherId=$this->request->getVar('voucherId');
        $data['voucherId']=$voucherId;
        $data['suppliers']=$db->table('suppliers')->get()->getResult();
        $data['title']="Add Voucher items";
        $data['items']=$db->table('check_voucher_items')->getWhere(['voucherId'=>$voucherId])->getResult();
        $data['voucher']=$db->table('cheque_voucher')->getWhere(['id'=>$voucherId])->getResultArray()[0];
        $data['itemId']=$db->insertID();
        return view('suppliers/cheque_voucher_items',$data);
    }

    public function delete_voucher_item($id,$voucherId){
        $db=Database::connect();
        $db->table('check_voucher_items')->delete(['id'=>$id]);
        $data['voucherId']=$voucherId;
        $data['suppliers']=$db->table('suppliers')->get()->getResult();
        $data['title']="Add Voucher items";
        $data['items']=$db->table('check_voucher_items')->getWhere(['voucherId'=>$voucherId])->getResult();
        $data['voucher']=$db->table('cheque_voucher')->getWhere(['id'=>$voucherId])->getResultArray()[0];
        $data['itemId']=$db->insertID();
        return view('suppliers/cheque_voucher_items',$data);
    }


    public function generate_voucher($id){
        $db=Database::connect();
        $data['ttl']="CHEQUE VOUCHER";
        $data['voucher']=$db->table('cheque_voucher')->getWhere(['id'=>$id])->getResultArray()[0];
        $data['items']=$db->table('check_voucher_items')->getWhere(['voucherId'=>$id])->getResultArray();


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

        $pdf->WriteHTML(view('suppliers/cheque_voucher_pdf',$data));
        $pdf->Output("Invoice-".$id."-".date('Y-m-d').".pdf","D");
        return ;
    }
}