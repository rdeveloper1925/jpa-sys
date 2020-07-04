<?php
namespace App\Controllers;

use CodeIgniter\Config\Services;
use Config\Database;
use Config\Validation;

class Users extends BaseController{
	public function index(){
	    $session=Services::session();
	    if($session->get('accessLevel')!='ADMINISTRATOR'){
	        return redirect()->to(base_url('pages/error'));
        }
		$db=Database::connect();
		$data['users']=$db->table('users')->get()->getResult('object');
		$data['title']="System Users";
		return view('content/users_view',$data);
	}

	public function reset_password($id){
        $session=Services::session();
        if($session->get('accessLevel')!='ADMINISTRATOR'){
            return redirect()->to(base_url('pages/error'));
        }
		$db=Database::connect();
		$db->table('users')->update(['password'=>'5f4dcc3b5aa765d61d8327deb882cf99']);//password
		Services::session()->setFlashdata('success','User password reset to "password"');
		return redirect()->to(base_url('users/'));
	}

	public function save(){
        $session=Services::session();
        if($session->get('accessLevel')!='ADMINISTRATOR'){
            return redirect()->to(base_url('pages/error'));
        }
		$info=array(
			'fullName'=>$this->request->getVar('fullName'),
			'username'=>strtoupper($this->request->getVar('username')),
			'password'=>hash('md5',$this->request->getVar('password')),
            'accessLevel'=>$this->request->getVar('accessLevel')
		);
		$db=Database::connect()->table('users')->insert($info);
		return redirect()->to(base_url('users/'));
	}

	public function changePassword(){
	    $id=Services::session()->get('id');

	    return view('content/change_password',['title'=>"Change Password"]);
    }

    public function save_password_change(){
        $id=Services::session()->get('id');
        $pwd=$this->request->getVar('password');
        $pwd2=$this->request->getVar('passwordRetype');
        if(strcmp($pwd,$pwd2)){
            return view('content/change_password',['title'=>"Change Password",'fail'=>"Passwords Must Match"]);
        }
        $chng=array(
            'password'=>hash('md5',$pwd)
        );
        $db=Database::connect();
        $db->table('users')->update($chng,['id'=>$id]);
        return view('content/change_password',['title'=>"Change Password",'success'=>'Password changed successfully']);
    }

    public function edit(){
        $session=Services::session();
        if($session->get('accessLevel')!='ADMINISTRATOR'){
            return redirect()->to(base_url('pages/error'));
        }
	    $db=Database::connect();
	    $id=$this->request->getVar('id');
	    $user=$db->table('users')->getWhere(['id'=>$id])->getResultArray()[0];
	    return json_encode($user);
    }

    public function update(){
        $session=Services::session();
        if($session->get('accessLevel')!='ADMINISTRATOR'){
            return redirect()->to(base_url('pages/error'));
        }
	    $db=Database::connect();
	    $id=$this->request->getVar('userId');
	    $edit=array(
	        'username'=>$this->request->getVar('username'),
            'fullName'=>$this->request->getVar('fullName'),
            'accessLevel'=>$this->request->getVar('accessLevel')
        );
	    $db->table('users')->update($edit,['id'=>$id]);
	    return redirect()->to(base_url('users/'));
    }

	public function delete($id){
        $session=Services::session();
        if($session->get('accessLevel')!='ADMINISTRATOR'){
            return redirect()->to(base_url('pages/error'));
        }
		$db=Database::connect();
		$db->table('users')->delete(['id'=>$id]);//password
		return redirect()->to(base_url('users/'));
	}
}
