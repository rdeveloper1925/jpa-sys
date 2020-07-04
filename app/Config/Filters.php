<?php namespace Config;

use App\Filters\Administrator;
use App\Filters\AuthFilter;
use App\Filters\UsersFilter;
use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
	// Makes reading things below nicer,
	// and simpler to change out script that's used.
	public $aliases = [
		'csrf'     => \CodeIgniter\Filters\CSRF::class,
		'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
		'honeypot' => \CodeIgniter\Filters\Honeypot::class,
        'authFilter'=>AuthFilter::class,
        'administrator'=>Administrator::class,
        'usersFilter'=>UsersFilter::class
	];

	// Always applied before every request
	public $globals = [
		'before' => [
			//'authFilter'=>['except'=>'auth*']
			// 'csrf',
		],
		'after'  => [
			'toolbar',
			//'honeypot'
		],
	];

	// Works on all of a particular HTTP method
	// (GET, POST, etc) as BEFORE filters only
	//     like: 'post' => ['CSRF', 'throttle'],
	public $methods = [];

	// List filter aliases and any before/after uri patterns
	// that they should run on, like:
	//    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
	public $filters = [
        'usersFilter'=>[
            //'before'=>['users/reset_password*','users/save*','users/edit*','users/update*','users/delete*']
        ],
		/*'accessFilter'=>[
			'before'=>['users*']
		],*/
		'authFilter'=>[
			'before'=>['invoices*','pages*','receipts*','inventory*']
		]

	];
}
