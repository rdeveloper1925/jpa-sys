<?php
namespace App\Filters;
use CodeIgniter\Config\Services;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;


class Administrator implements FilterInterface{

    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param \CodeIgniter\HTTP\RequestInterface $request
     *
     * @return mixed
     */
    public function before(RequestInterface $request)
    {
        $session=Services::session();
        if(!$session->get('accessLevel')){
            $session->setFlashdata('error','Please Login to Access page');
            return redirect()->to(base_url('auth/'));
        }
        if (!strcmp($session->get('accessLevel'),'ADMINISTRATOR')){
            return view('error',['title'=>'UNAUTHORIZED ACCESS','message'=>'You are not allowed to view this page']);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param \CodeIgniter\HTTP\RequestInterface $request
     * @param \CodeIgniter\HTTP\ResponseInterface $response
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response)
    {
        // TODO: Implement after() method.
    }
}