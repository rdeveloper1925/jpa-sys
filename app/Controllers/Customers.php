<?php
namespace App\Controllers;
use Config\Database;
use org\bovigo\vfs\vfsStreamContainerIterator;

class Customers extends BaseController{
    public function index(){
        $db=Database::connect();
        $customers=$db->table('customers')->getWhere(['deleted'=>0])->getResult('object');
        $data['customers']=$customers;
        $data['title']='Registered Customers';
        return view('content/customers_view',$data);
    }
    public function save(){
        $customer=array(
            'customerName'=>$this->request->getVar('customerName'),
            'contactPerson'=>$this->request->getVar('contactPerson'),
            'tinNo'=>$this->request->getVar('tinNo'),
            'address'=>$this->request->getVar('address'),
            'areaCountry'=>$this->request->getVar('areaCountry'),
            'phone'=>$this->request->getVar('phone'),
            'email'=>$this->request->getVar('email'),
            'deleted'=>0
        );
        //return print_r($customer);
        $db=Database::connect();
        $db->table('customers')->insert($customer);
        return redirect()->to(base_url('customers/'));
    }
    public function edit($id){
        $db=Database::connect();
        $customer=$db->table('customers')->getWhere(['id'=>$id,'deleted'=>0])->getResult('object');
        if(empty($customer)){
            return redirect()->to(base_url('customers/'));
        }
        $data['customer']=$customer[0];
        $data['title']='Edit Customer';
        return view('content/view_customer',$data);
    }
    public function update($id){
        $db=Database::connect();
        $customer=array(
            'customerName'=>$this->request->getVar('customerName'),
            'contactPerson'=>$this->request->getVar('contactPerson'),
            'tinNo'=>$this->request->getVar('tinNo'),
            'address'=>$this->request->getVar('address'),
            'areaCountry'=>$this->request->getVar('areaCountry'),
            'phone'=>$this->request->getVar('phone'),
            'email'=>$this->request->getVar('email')
        );
        $db->table('customers')->update($customer,['id'=>$id,'deleted'=>0]);
        return redirect()->to(base_url('customers/'));
    }
    public function delete($id){
        $db=Database::connect();
        $db->table('customers')->update(['deleted'=>1],['id'=>$id]);
        return redirect()->to(base_url('customers/'));
    }
}