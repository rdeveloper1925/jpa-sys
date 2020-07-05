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
            'balance'=>0
        );
        $db->table('suppliers')->insert($supplier);
        return redirect()->to(base_url('suppliers/'));
    }
}