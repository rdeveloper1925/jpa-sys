<?php
namespace App\Controllers;

use Config\Database;
use Config\Services;

class Inventory extends BaseController{
	public function index(){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='STORE-KEEPER') {
            $db=Database::connect();
            $inventory=$db->table('inventory')->orderBy('partName', 'ASC')->orderBy('balanceInStore', 'ASC')->get()->getResult('object');
            return view('content/inventory', ['title'=>'Inventory / Stock', 'inventory'=>$inventory]);
        }
        return redirect()->to(base_url('pages/error'));
	}

	public function save(){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='STORE-KEEPER') {
            $inventory=array(
                'id'=>null,
                'dateIn'=>$this->request->getVar('dateIn'),
                'partName'=>$this->request->getVar('partName'),
                'partNo'=>$this->request->getVar('partNo'),
                'dateOut'=>null,
                'quantityInStore'=>$this->request->getVar('quantityInStore'),
                'balanceInStore'=>$this->request->getVar('quantityInStore'),
                'suppliedBy'=>$this->request->getVar('suppliedBy'),
                'takenBy'=>null,
                'unitOfMeasure'=>$this->request->getVar('unitOfMeasure')
            );
            //return print_r($inventory);
            $db=Database::connect();
            //return $db->table('inventory')->set($inventory)->getCompiledInsert();
            $db->table('inventory')->insert($inventory);
            $itemId=$db->insertID();
            $trigger=array(
                'itemName'=>$inventory["partName"],
                'inputBy'=>'INVENTORY'
            );
            $db->table('inventorytemp')->insert($trigger);
            
            $track=array(
                'itemId'=>$itemId,
                'quantityBefore'=>0,
                'stockAction'=>"New Inventory Item",
                'quantity'=>$this->request->getVar('quantityInStore'),
                'quantityAfter'=>$this->request->getVar('quantityInStore'),
                'date'=>$this->request->getVar('dateIn'),
                'doneBy'=>Services::session()->get('id')
            );
            $db->table('stockTracker')->insert($track);
            return redirect()->to(base_url('inventory'));
        }
        return redirect()->to(base_url('pages/error'));
	}

	public function delete_inventory($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='STORE-KEEPER') {
            $db=Database::connect();
            $db->query('SET FOREIGN_KEY_CHECKS=0');
            $db->table('inventory')->delete(['id'=>$id]);
            return redirect()->to(base_url('inventory'));
        }
        return redirect()->to(base_url('pages/error'));
	}

	public function edit($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='STORE-KEEPER') {
            $db=Database::connect();
            $item=$db->table('inventory')->getWhere(['id'=>$id])->getResult('object');
            if (empty($item)) {
                return redirect()->to(base_url('inventory/'));
            }
            $data['item']=$item[0];
            $data['title']="Adjust Inventory Item";
            return view('content/edit_inventory', $data);
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function update($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='STORE-KEEPER') {
            $db=Database::connect();
            if ($this->request->getVar('balanceInStore') > $this->request->getVar('quantityInStore')) {
                return redirect()->to(base_url('inventory'));
            }
            $inventory=array(
                'dateIn'=>$this->request->getVar('dateIn'),
                'partName'=>$this->request->getVar('partName'),
                'partNo'=>$this->request->getVar('partNo'),
                'quantityInStore'=>$this->request->getVar('quantityInStore'),
                'balanceInStore'=>$this->request->getVar('quantityInStore'),
                'suppliedBy'=>$this->request->getVar('suppliedBy'),
                'unitOfMeasure'=>$this->request->getVar('unitOfMeasure')
            );
            $info=array(
                'balanceInStore'=>$this->request->getVar('balanceInStore'),
                'quantityInStore'=>$this->request->getVar('quantityInStore'),
                'suppliedBy'=>$this->request->getVar('suppliedBy'),
                'unitOfMeasure'=>$this->request->getVar('unitOfMeasure'),
                'dateIn'=>$this->request->getVar('dateIn'),
                'partName'=>$this->request->getVar('partName'),
                'partNo'=>$this->request->getVar('partNo')
            );
            $db->table('inventory')->update($info, ['id'=>$id]);
            return redirect()->to(base_url('inventory/'));
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function see($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='STORE-KEEPER') {
            $db=Database::connect();
            $item=$db->table('inventory')->getWhere(['id'=>$id])->getResult('object');
            $log=$db->table('stockTracker')->select('inventory.partName,stockTracker.quantityBefore,stockTracker.stockAction,stockTracker.quantity,stockTracker.quantityAfter,stockTracker.date,users.fullName')
                ->join('inventory', 'stocktracker.itemId=inventory.id', 'inner')
                ->join('users', 'stockTracker.doneBy=users.id', 'inner')
                ->orderBy('date', 'DESC')->getWhere(['itemId'=>$id])->getResult('object');
            $data['item']=$item[0];
            $data['title']="Inventory Details";
            $data['log']=$log;
            return view('content/see_inventory', $data);
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function restock($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='STORE-KEEPER') {
            $db=Database::connect();
            $rs=$db->table('inventory')->getWhere(['id'=>$id])->getResult('object')[0];
            $oldqty=$rs->quantityInStore;
            $oldbal=$rs->balanceInStore;

            $restock=array(
                'quantityInStore'=>$oldqty + $this->request->getVar('quantity'),
                'balanceInStore'=>$oldbal + $this->request->getVar('quantity')
            );
            $db->table('inventory')->update($restock, ['id'=>$id]);
            $track=array(
                'itemId'=>$id,
                'stockAction'=>'Re-Stock',
                'quantityBefore'=>$oldqty,
                'quantity'=>$this->request->getVar('quantity'),
                'quantityAfter'=>$oldqty + $this->request->getVar('quantity'),
                'date'=>date('Y-m-d H:i:s'),
                'doneBy'=>Services::session()->get('id')
            );
            $db->table('stockTracker')->insert($track);
            return redirect()->to(base_url('inventory/see/' . $id));
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function adjust($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='STORE-KEEPER') {
            $db=Database::connect();
            $rs=$db->table('inventory')->getWhere(['id'=>$id])->getResult('object')[0];
            $oldqty=$rs->quantityInStore;
            $adjustment=array(
                'quantityInStore'=>$this->request->getVar('quantity'),
                'balanceInStore'=>$this->request->getVar('quantity')
            );
            $db->table('inventory')->update($adjustment, ['id'=>$id]);
            $track=array(
                'itemId'=>$id,
                'stockAction'=>'Manual Adjustment of Stock',
                'quantityBefore'=>$oldqty,
                'quantity'=>$this->request->getVar('quantity'),
                'quantityAfter'=>$this->request->getVar('quantity'),
                'date'=>date('Y-m-d H:i:s'),
                'doneBy'=>Services::session()->get('id')
            );
            $db->table('stockTracker')->insert($track);
            return redirect()->to(base_url('inventory/see/' . $id));
        }
        return redirect()->to(base_url('pages/error'));
    }

    public function record_sale($id){
        $session=\CodeIgniter\Config\Services::session();
        $l=$session->get('accessLevel');
        if($l=='ADMINISTRATOR'||$l=='STORE-KEEPER') {
            $db=Database::connect();
            $rs=$db->table('inventory')->getWhere(['id'=>$id])->getResult('object')[0];
            $oldqty=$rs->quantityInStore;
            $adjustment=array(
                'quantityInStore'=>$this->request->getVar('qtyInStore'),
                'balanceInStore'=>$oldqty-$this->request->getVar('quantity')
            );
            $db->table('inventory')->update($adjustment, ['id'=>$id]);
            $track=array(
                'itemId'=>$id,
                'stockAction'=>'Sale of Stock',
                'quantityBefore'=>$oldqty,
                'quantity'=>$this->request->getVar('quantity'),
                'quantityAfter'=>$oldqty-$this->request->getVar('quantity'),
                'date'=>date('Y-m-d H:i:s'),
                'doneBy'=>Services::session()->get('id')
            );
            $db->table('stockTracker')->insert($track);
            return redirect()->to(base_url('inventory/see/' . $id));
        }
        return redirect()->to(base_url('pages/error'));
    }
}
