<?php
  use Minishlink\WebPush\WebPush;
  use Minishlink\WebPush\Subscription;
  use Minishlink\WebPush\VAPID;
  class GLAlertsPushNotification  extends GLibrary{
    public function getkey()
    {
        $res = $this->webPushModel->getPushKeys();
        
        $this->setOutputHeader(['Content-Type: application/json']);
        $this->setOutput(json_encode(['status'=>true,'message'=>'Push key retrieved successfully','data'=>$res['public']]));
    }
    

    private function getPushKeys()
    {
      $config = [
        'public' => PUSH_PUBLIC_KEY, // don't forget that your public key also lives in app.js
        'private' => PUSH_PRIVATE_KEY,
      ];
      if( strlen($config['public']) < 1 ||  strlen($config['private']) < 1 ){
        $config = $this->generateEncryptionKeys();
      }
      return $config;
    }

    private function generateEncryptionKeys(){
      $keys = VAPID::createVapidKeys(); // TODO: write keys to config  
      return ['public'=>$keys['publicKey'],'private'=>$keys['privateKey']];
    }

    public function sendWebPush($subscriptionToken,$subject,$message,$image,$meta = [])
    {
      $config = $this->getPushKeys();
  
      // here I'll get the subscription endpoint in the POST parameters
      // but in reality, you'll get this information in your database
      // because you already stored it (cf. push_subscription.php)
      $subscriptionToken = html_entity_decode($subscriptionToken);
      $subscription = Subscription::create(json_decode($subscriptionToken, true));

      $data = ['messgae'=>$message,'image'=>$image,'meta'=>$meta,'subject']=>$subject];

      $auth = array(
          'VAPID' => array(

              'subject' =>$data['url'],
              'publicKey' => $config['public'], // don't forget that your public key also lives in app.js
              'privateKey' => $config['private'], // in the real world, this would be in a secret file
          ),
      );
      $webPush = new WebPush($auth);
      $report = $webPush->sendOneNotification($subscription,json_encode($data));
      // handle eventual errors here, and remove the subscription from your server if it is expired
      $endpoint = $report->getRequest()->getUri()->__toString();

      if ($report->isSuccess()) {
          return ['status'=>true,'message'=> "[v] Message sent successfully for subscription {$endpoint}."];
      } else {
          return ['status'=>false,'message'=> "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}"];
      }
    }

    public function addPushToken($userId,$token,$platform)
    {
      return $this->db->insert($this->tokensTableName,['user_id'=>$userId,'platform'=>$platform,'token'=>$token]);
    }

    public function getPushTokens($fields = [])
    {
      return $this->db->query->($this->tokensTableName, array_merge($fields,['token','user_id AS userId','platform']));
    }

    public function sendUserPushNotification($userId,$subjects,$message,$image,$platform = '*',$meta = [])
    {
      $base   = $this->getUserPushTokens()->where_equal('status',$status)->and_where('user_id',$userId);
      $tokens =  $platform == '*' ? $base->exec()->rows : $base->and_where('platform',$platform)->exec()->rows;
      count($tokens) < 1 && return  ['status'=>false,'message'=>'User has no registered tokens','data'=>[ 'attempts'=>0]
      $success = []; 
      $failed  = []; 
      foreach($tokens AS $item){
        $delivery =  $item['platform'] == 'web' ? $this->sendWebPush($item['token'],$subject,$message,$image,$meta) : $this->sendMobilePush($item['token'],$subject,$message,$image,$meta);
        $delivery['status'] ? array_push($item['token'],$success) : array_push($item['token'],$failed);
      }
      
      return count($success) > 0 ?  ['status'=>true,'message'=>'Sent atleast one notice','data'=>['success'=>$success,'failed'=>$failed]] :  ['status'=>false,'message'=>'Unable to send alerts','data'=>['success'=>$success,'failed'=>$failed,'attempts'=>count($success)+count($failed)]];
    }
   

    
    
  }
?>