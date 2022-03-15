<?php
	class GRSeller Extends GRoute{
		public function index(){
			echo 'worked';
		}

		public function register(){
			$this->load->controller('seller');
			$postFields = $this->http->post(['tradeid','storeid','storename','city','country']);
			$res = $this->controller_seller->addSeller($postFields['tradeid'],$postFields['storeid'],$postFields['storename'],$postFields['city'],$postFields['country']);
			$this->http->emit($res);
		}

	}
?>