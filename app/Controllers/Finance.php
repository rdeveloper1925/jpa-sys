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
        //checking if invoice already exists
        $taxInvoiceNo=$this->request->getVar('taxInvoiceNo');
        $proformaNo=$this->request->getVar('proformaNo');
        $rs=$db->query("SELECT * FROM finance WHERE proformaNo=$proformaNo OR taxInvoiceNo=$taxInvoiceNo")->getResultArray();
        if(!empty($rs)){
            session()->setFlashdata('fail',"The supplied tax Invoice number or proforma number already exists");
            return redirect()->to(base_url('finance'));
        }
        $entry=array(
            'date'=>$this->request->getVar('date'),
            'proformaNo'=>$this->request->getVar('proformaNo'),
            'taxInvoiceNo'=>$this->request->getVar('taxInvoiceNo'),
            'lpoNo'=>$this->request->getVar('lpoNo'),
            'customerId'=>$this->request->getVar('customerId'),
            'customerName'=>$this->request->getVar('customerName'),
            'confirmed'=>$this->request->getVar('confirmed'),
            'withholdingTax'=>$this->request->getVar('withholdingTax'),
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
        $entry=$db->table('finance')->getWhere(['id'=>$id])->getResultObject()[0];
        $data['success']=1;
        $data['entry']=$entry;
        return json_encode($data);
    }

    public function update(){
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/Finance.log', Logger::INFO));
        $entryId=$this->request->getVar('entryId');
        $db=Database::connect();
        $entry=array(
           // 'date'=>$this->request->getVar('date'),
            'proformaNo'=>$this->request->getVar('proformaNo'),
            'taxInvoiceNo'=>$this->request->getVar('taxInvoiceNo'),
            'lpoNo'=>$this->request->getVar('lpoNo'),
            'customerId'=>$this->request->getVar('customerId'),
            'customerName'=>$this->request->getVar('customerName'),
            'confirmed'=>$this->request->getVar('confirmed'),
            'withholdingTax'=>$this->request->getVar('withholdingTax'),
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
        $db->table('finance')->update($entry,['id'=>$entryId]);
        $logger->info("Finance entry edited successfully by ",['maker'=>session()->get('fullName')]);
        session()->setFlashdata('success','Entry edited successfully');
        return redirect()->to(base_url('finance'));
    }

    public function delete($id=0){
        $db=Database::connect();
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/Finance.log', Logger::INFO));
        $db->table('finance')->delete(['id'=>$id]);
        $logger->info("Financial entry with id=$id has been deleted",['maker'=>session()->get('fullName')]);
        return json_encode(['success'=>1,'message'=>'Financial entry deleted successfully']);
    }

    public function view_report(){
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/Finance.log', Logger::INFO));
        $db=Database::connect();
        //return json_encode($this->request->getPost());
        return view('visuals/index',['title'=>'visualizations']);
    }
}