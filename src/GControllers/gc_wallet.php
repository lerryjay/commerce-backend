<?php 
	class GCWallet extends GController{

		public function index(){ 
 			 echo 'wallet was created successfully'; 
		}
		
		public function initiatecredit()
		{

		}

		public function completePayment()
		{

		}
			
		public function credit()
		{
			$paymentReference = '';
			//check user is authenticated
			extract($data);
			//check reference is valid and belongs to user 
			$refenceExists = $this->searchTransactions(['reference'=>$paymentReference]);
			

			$insert = $this->model_wallet->addWalletTransaction($user['id'],$amount,$debit,$description,$reference,$channel);
			!$insert && $this->request->emit(['status'=>false,'message'=>'Error funding wallet']);

			$timestamp = date('Y-m-d H:i:s');
			//send email notification
			$this->load->library('alert/mail');
			$mailTemplate  = $this->load->view('transaction/credit',['amount'=>$amount,'user'=>$user,'reference'=>$paymentReference,'timestamp'=>$timestamp]);
			$this->library_alerts_mail->send($user['email'],APP_NAME." Credit Transaction ",$mailTemplate);
			
			//send sms notification --complete actual library
			$this->load->library('alert/sms');
			$this->library_alerts_send->sms($user['telephone'],APP_NAME." Credit Transaction ","Wallet successfully funded with $mount. Your new balance is $balance");

			//send push notification
			$this->load->library('alert/push_notification',true);
			$this->library_alert_push_notification->sendUserPushNotification($userId['id'],APP_NAME." Credit Transaction ','Your account was credited $mount at $timestamp new balance $balance reference $reference",APP_LOGOURL,$platform = '*',['type'=>'credit']);
 
			$balance =  $this->model_wallet->getUserWalletBalance($user['id']);
			$this->request->emit(['status'=>true,'message'=>"Wallet successfully funded with $mount. Your new balance is $balance",["data"=>$balance]]);

		}

		public function balance()
		{

		}
 	} 
 ?> 