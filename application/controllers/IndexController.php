<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $client_obj = new Application_Model_Client();
        if( isset( $_POST['send_msg'] )  )
        {
            if(!$client_obj->insertMsg( $_POST))
            {
                $_SESSION['error'] = "Error in message sending !!";
            }
            else
            {
                $_SESSION['success'] = "Message sent successfully";
            }
            $this->_redirect('index/#footer-m');
        }
    }

    public function loginAction()
    {
        $client_obj = new Application_Model_Client();
        if( isset( $_POST['login'] )  )
        {
            if(!$client_obj->authenticate( $_POST))
            {
               // $_SESSION['error'] = "Invalid Username/Password !!";
                $this->_redirect('index/login');
            }
            $this->_redirect('client/gallery');

        }
    }

    public function signoutAction()
    {
        // action body
        Zend_Session::destroy();
        unset($_SESSION);
        $this->_redirect('index/login');
    }
    
    public function notificationAction()
    {
        // action body
    }

    public function registerAction()
    {
        $client_obj = new Application_Model_Client();
        if( isset( $_POST['register'] )  )
        {
            if(!$client_obj->insertRegister( $_POST))
            {
                $_SESSION['error'] = "Error in Registration !!";
            }
            else
            {
                $_SESSION['success'] = "Registration success";
            }
            $this->_redirect('index/register');
        }


    }



}





