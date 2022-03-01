<?php 
  class GLAlertsMail extends GLibrary
  {


    public function send($email,$subject,$template,$replyTo = EMAIL_INFO_ACCOUNT,$from = EMAIL_INFO_ACCOUNT){
      $headers  = 'MIME-Version: 1.0'."\r\n";
      $headers  .= 'Content-Type: text/html; charset=iso-8859'."\r\n";
      $headers  .= 'From: '.$from."\r\n";
      $headers  .= 'Reply-To: '.$replyTo."\r\n";
      return mail($email,$subject,$template);
    }

    public function pushnotification($token,$message){
      return;
    }
  }
?>