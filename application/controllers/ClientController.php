<?php

class ClientController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        
        if(!isset($_SESSION["user_id"]))
        $this->_redirect('index/login');
    }

    public function indexAction()
    {
        // action body
    }

    public function notificationListAction()
    {
        $client_obj = new Application_Model_Client();
        if( isset( $_POST['create_notification'] )  )
        {
            //print_r($_POST);die();
            if(!$client_obj->insertNotification( $_POST))
            {
                $_SESSION['error'] = "Notification insertion has an error !!";
            }else
                $_SESSION['success'] = "Notification inserted successfully !!";

            $this->_redirect('client/notification-list');

        }elseif( isset( $_POST['edit_notification'] ) && isset($_POST['notification_id']) )
        {
            if(!$client_obj->updateNotification($_POST['notification_id'],$_POST))
            {
                $_SESSION['error'] = "Notification insertion has an error !!";
            }
            else
                $_SESSION['success'] = "Notification updated successfully !!";
            $this->_redirect('client/notification-list');

        }elseif( isset( $_POST['delete_notification'] ) && isset($_POST['notification_id'])   )
        {
            if(!$client_obj->deleteNotification( $_POST['notification_id']))
            {
                $_SESSION['error'] = "Notification deletion has an error !!";
            }
            else
                $_SESSION['success'] = "Notification deleted successfully !!";
            $this->_redirect('client/notification-list');

        }
    }

    public function notificationAction()
    {
        // action body
    }

    public function loginAction()
    {
        // action body
    }
    public function galleryAction()
    {
        $client_obj = new Application_Model_Client();
        $file_list =$client_obj->getUploadedFilesList();


        if(isset($_POST['file_upload']))
        {
            if($client_obj->getRowCount($file_list)>9)
            {
                $_SESSION['error']="Error! Upload image limit is 10 !";
                $this->_redirect('client/gallery');
            }
            elseif($_FILES['filename']['size'] > 6048576)
            {
                $_SESSION['error']="Error! Upload file size is greater than 5 MB !";
                $this->_redirect('client/gallery');
            }
            elseif(!$client_obj->UploadFiles($_POST))
            {
                $_SESSION['error']="File Upload has an error !";
                $this->_redirect('client/gallery');
            }
            
            else
            {
                $_SESSION['success']="File Uploaded Successfully";
                $this->_redirect('client/gallery');
            }
        }
        elseif(isset($_POST['remove_uploaded_file']) && isset($_POST['upload_id']) && is_numeric($_POST['upload_id']))
        {
            if(!$client_obj->removeUploadFiles($_POST['upload_id']))
            {
                $_SESSION['delete_error']="File deletion has an error!!";
            }else
            {
                $_SESSION['delete_success']="File deleted successfully!!";
            }
                $this->_redirect('client/gallery');
        }
    }

    public function registrationAction()
    {
        // action body
    }

}







