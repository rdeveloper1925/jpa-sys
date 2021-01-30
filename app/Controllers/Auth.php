<?php namespace App\Controllers;

use CodeIgniter\Config\Services;
use Config\Database;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Auth extends BaseController{
	public function index(){
        return view('layouts/login');
	}
    public function doLogin(){
        $logger=new Logger('errors');
        $logger->pushHandler(new StreamHandler('Logs/auth.log', Logger::INFO));
		$session=Services::session();
       	$username=$this->request->getVar('username');
		$password=hash('md5',$this->request->getVar('password'));
        $db=Database::connect();
        $rs=$db->table('users')->getWhere(['username'=>$username,'password'=>$password])->getResultArray();
        if (empty($rs)){
        	$session->setFlashdata('error','Username and password dont match!');
            $logger->info("Failed password ",['username'=>$username]);
			return view('layouts/login');
		}
        //print_r($rs[0]);
        $session->set($rs[0]);
        return redirect()->to(base_url('pages'));
    }

    public function preliminarye(){
		$info=array(
			'fullName'=>'Administrator',
			'username'=>'admin',
			'password'=>hash('md5','password')
		);
		//password== 5f4dcc3b5aa765d61d8327deb882cf99
		$db=Database::connect()->table('users')->insert($info);
		echo 'ok';
	}

	public function logout(){
		Services::session()->destroy();
		return redirect()->to(base_url('auth/'));
	}
	public function error_page(){
		return view('error',['message'=>'You are not authorized to view this page','title'=>'Unauthorized Access']);
	}
}
