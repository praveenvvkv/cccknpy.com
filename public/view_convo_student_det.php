<?php
//die("fdfdfd");
ini_set("display_errors",1);
session_start();
require_once ( '../application/models/Database.php' );
require_once ( '../application/models/Util.php' );
require_once ( '../application/models/Client.php' );

$util=new Application_Model_Util();
$db=new Application_Model_Database();
$client_obj = new Application_Model_Client();


$notification_id=0;
if(isset($_GET['notification_id']) && is_numeric($_GET['notification_id']) && $_GET['notification_id']>0)
    $notification_id=$_GET['notification_id'];
else
    die();
    
$notification_det = $client_obj->getNotification($notification_id);
//print_r($notification_det);
?>
<form method="post" name="form_edit" id="form_edit" class="form-horizontal not_edit">
        <input type='hidden' name='notification_id' value="<?php echo $notification_det['notification_id']; ?> ">
        <div class="control-group" style="width:100%">
            <label class="control-label" for="form-field-1"><h4>Heading</h4></label>

            <div class="controls">
                <textarea id="form-field-1" placeholder="Heading" type="text"   name="notification_header"><?php echo $notification_det["notification_header"]; ?></textarea>
            </div>
        </div>
        <input type="hidden"  name="notification_desc" id="notification_desc123"  value="<?php echo $notification_det['notification_desc']; ?> ">
        <textarea class="txtEditor" id="txtEditor123" onchange="alert();" ><?php echo $notification_det["notification_desc"]; ?></textarea><br><br>
        
        <div class="form-actions">
            <button class="btn btn-success btn-sm" type="submit" name="edit_notification" onclick="getHtml123();"><i class="icon-ok bigger-110"></i>Update</button>
            <button class="btn btn-sm btn-primary" type="button" onclick='$( ".not_edit" ).hide();$( ".not_view" ).show();'><i class="icon-undo bigger-110"></i>Cancel</button>
        </div>
    </form>