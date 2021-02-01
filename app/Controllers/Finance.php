<?php
namespace App\Controllers;
use Config\Database;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use org\bovigo\vfs\vfsStreamContainerIterator;

class Finance extends BaseController{
    public function match(){
        $db=Database::connect();
        $proformae=$db->table('proforma');
    }

    public function index(){
        $db=Database::connect();
        $customers=$db->table('customers')->get()->getResultObject();
        $finances=$db->table('finance')->
        select('*')->orderBy('id', 'DESC')->get()->getResult('object');
        return view('finance/index', ['title'=>'Finances', 'finances'=>$finances,'customers'=>$customers]);
    }

    public function save(){
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/Finance.log', Logger::INFO));
        $db=Database::connect();
        $entry=array(
            'date'=>$this->request->getVar('date'),
            'proformaNo'=>$this->request->getVar('proformaNo'),
            'taxInvoiceNo'=>$this->request->getVar('taxInvoiceNo'),
            'lpoNo'=>$this->request->getVar('lpoNo'),
            'customerId'=>$this->request->getVar('customerId'),
            'customerName'=>$this->request->getVar('customerName'),
            'confirmed'=>$this->request->getVar('confirmed'),
            'withholdingTax'=>$this->request->getVar('witholdingTax'),
            'vat'=>$this->request->getVar('vat'),
            'totalPayable'=>$this->request->getVar('totalPayable'),
            'cleared'=>$this->request->getVar('cleared'),
            'email'=>$this->request->getVar('email'),
            'phone'=>$this->request->getVar('phone'),
            'areaCountry'=>$this->request->getVar('areaCountry'),
            'address'=>$this->request->getVar('address'),
            'tinNo'=>$this->request->getVar('tinNo'),
            'contactPerson'=>$this->request->getVar('contactPerson')
        );
        $db->table('finance')->insert($entry);
        $logger->info("New financial entry made",['maker'=>session()->get('fullName')]);
        session()->setFlashdata('success','New financial Entry has been entered');
        return redirect()->to(base_url('finance'));
    }

    public function fetchEntry(){
        $db=Database::connect();
        $id=$this->request->getVar('id');
        $entry=$db->table('finance')->getWhere(['id'=>1])->getResultObject()[0];
        $data['success']=1;
        $data['entry']=$entry;
        return json_encode($data);
    }
}