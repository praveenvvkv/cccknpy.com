<?php


class Application_Model_Client extends Application_Model_Database
{
    public $util;
    private $message_text;
    private $error_text;
    function __construct($args = null)
    {
        parent::__construct($args);
        $this->util = new Application_Model_Util();
    }
    function insertNotification($arr)
    {
        $notification_id = $this->getNextId("notification_tb", "notification_id",false);

        $arrayInsert = array(
            "notification_id" => $notification_id,
            "notification_header" => $arr["notification_header"],
            "notification_desc" => $arr["notification_desc"],
            "user_id" => $_SESSION['user_id'],
            "notification_on" => $this->util->getCurDateTime()

        );
        return $this->insert("notification_tb", $arrayInsert,false);
    }
    function updateNotification($notification_id,$arr)
    {
        $arrayInsert = array(
            "notification_header" => $arr["notification_header"],
            "notification_desc" => $arr["notification_desc"]
        );
        return $this->update("notification_tb", $arrayInsert,array("notification_id" => $notification_id),false);
    }
    function deleteNotification($notification_id)
    {
        return $this->delete("notification_tb", array("notification_id" => $notification_id),false);
    }
    function getNotificationList()
    {
        $sql = "SELECT * FROM notification_tb ORDER BY notification_id DESC ";
        return $this->query($sql);
    }
    function getNotification($notification_id)
    {
        $sql = "SELECT * FROM notification_tb WHERE notification_id=:notification_id  ";
        $st = $this->query($sql,array("notification_id"=>$notification_id));
        if( $row = $this-> fetch($st) )
        {
            return $row;
        }
        else 
        return null;
    }
    function authenticate($arr)
    {
        $query = "SELECT * FROM user_tb WHERE user_login=:user_login AND user_password=:user_password AND user_status='Active'";
        $st = $this->query($query,array("user_login"=>$arr['user_login'],"user_password"=>$arr['user_password']));
        if( $row = $this-> fetch($st) )
        {
            $_SESSION['user_id']=$row['user_id'];
            $_SESSION['name']=$row['user_name'];
            return true;
        }
        return false;
    }
    //=========================== Gallery ===========================
    
    function getUploadedFilesList()
    {
        $sql = " SELECT * FROM  gallery_tb ORDER BY upload_id DESC ";
        return $this->query($sql);

    }
    
    public function UploadFiles($args)
    {

        $upload_id = $this->getNextId("gallery_tb","upload_id",false);
        $upload_path = 'uploads/gallery/';
        $filename = $_FILES['filename']['name'];
        $old_f_name = $filename;
        $ext_arr = explode(".", $filename);
        $ext = "";
        if (count($ext_arr) <= 1) {
            return false;
        } else if (count($ext_arr) > 1) {
            $ext = end($ext_arr);
        }
        if (!empty($ext))
        {
            $allowed =  array('jpg','JPG','jpeg','JPEG','png','PNG');
            if(!in_array($ext,$allowed))
            {
                $_SESSION['type_error']="Invalid file extension. !";
                return false;
            }

            $new_f_name = date('Ymd') . time() . "." . $ext;
            while (file_exists($upload_path . $new_f_name))
            {
                $new_f_name = date('Ymd') . time() . "." . $ext;
            }
            if (move_uploaded_file($_FILES['filename']['tmp_name'], $upload_path . $new_f_name))
            {
                if (!$this->insert("gallery_tb",array("upload_id"=>$upload_id,"upload_name"=>$old_f_name,"new_name"=>$new_f_name,"user_id"=>$_SESSION["user_id"],"uploaded_on"=>$this->util->getCurDateTime()),false))
                    return false;
            }
            return true;

        }
        return false;
    }
    
    public function removeUploadFiles($upload_id)
    {
        return $this->delete("gallery_tb",array("upload_id"=>$upload_id),false);
    }
    
    //============================= msg ==============================
    
    function insertMsg($arr)
    {
        $msg_id = $this->getNextId("msg_tb", "msg_id",false);

        $arrayInsert = array(
            "msg_id" => $msg_id,
            "msg_name" => $arr["msg_name"],
            "msg_email" => $arr["msg_email"],
            "msg" => $arr['msg'],
            "msg_on" => $this->util->getCurDateTime()

        );
        return $this->insert("msg_tb", $arrayInsert,false);
    }

    //============================= registration ==============================

    function insertRegister($arr)
    {
        $register_id = $this->getNextId("register_tb", "register_id",false);

        $arrayInsert = array(
            "register_id" => $register_id,
            "student_name" => $arr["student_name"],
            "phone_no" => $arr["phone_no"],
            "school_name" => $arr["school_name"],
            "select_course" => $arr['select_course'],
            "done_on" => $this->util->getCurDateTime()

        );
        return $this->insert("register_tb", $arrayInsert,false);
    }


}

