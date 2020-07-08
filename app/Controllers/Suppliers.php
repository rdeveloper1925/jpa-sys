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
        $result=$db->table('supplier_ledgers')->getWhere(['supplier_id'=>$id])->getResult('object');
        $data['ledger_items']=$result;
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
}