<?php
	class GRUser Extends GRoute{
		public function index(){
      // echo 'worked';
      $this->load->library('encrypt');
      // echo $this->library_encrypt->generate_api_private_key();
		}

		private function register(){
			$postFields = $this->http->post(array('username','telephone','email','password'));
			$this->load->controller('user');
			$res = $this->controller_user->addUser($postFields['username'],$postFields['email'],$postFields['telephone'],$postFields['password']);
			$this->http->emit($res);
		}

		public function login(){
			$postFields = $this->http->post(array('loginid','password'));
			$this->load->controller('user');
			$res = $this->controller_user->loginUser($postFields['loginid'],$postFields['password']);
			$this->http->emit($res);
		}

		public function balance(){
			$user = $this->load->controller('user');
			$trasaction = $this->load->controller('transactions');
			$userId = $this->http->sanitize("jire");
			$userId = $this->http->validateApiKey();
			// $ = $user->decodeApiKey($key);
			$balance = $trasaction->getUserBalance($userId);
			$this->http->emit(['status'=>true,'balance'=>$balance,'message'=>"Your balance is $balance"]);
		}

		public function textt(){
			$seller = $this->load->controller('seller');
			$seller->getSellerByName();
		}

		public function creditWallet(){
			$user = $this->load->controller('user');
			$trasaction = $this->load->controller('transactions');
		}
	}
?>