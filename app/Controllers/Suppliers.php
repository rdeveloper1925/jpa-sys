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
}