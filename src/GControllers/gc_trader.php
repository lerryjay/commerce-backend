<?php 
  class GCTrader Extends GController{


  	public function addTrader($userId,$storename,$storecategory,$address,$city,$country,$shortInfo,$storeslogan)
  	{
      $this->validateseller($storename,$storecategory,$address,$city,$country);
      $sellerm = $this->load->model('seller');
	  	
    }

    public function updateTrader($tradeId,$name,$gender,$city,$country)
  	{
      $this->load->model('trader');
      $this->load->model('users');
      $userExists = $this->model_users->getUserbyTradeId($tradeId);
      if(!$userExists['status']) return [ "error"=>true, "message"=>"Invalid User"];
      $validData = $this->validateTrader($name,$gender,$city,$country);
      if(!$validData['status']) return $validData;
      return $this->model_trader->updateTrader($userExists['data']['userid'],$name,$gender,$city,$country);  
    }

    public function validateTrader($name,$gender,$city,$country)
    {
      $this->load->library('validator');
      $this->load->model('global');
      $genders = ["male","female","ns"];
      if($this->library_validator->cleanString($name,3,200)) return ['error'=>true,'message'=>'Invalid Name, Only letters and spaces allowed','field'=>'name'];
      if(!in_array(strtolower($gender),$genders)) return ['error'=>true,'message'=>'Can only be male,female or ns (Non specified)','field'=>'gender'];
      $cityExists = $this->model_global->getCityById($city);
      if(!$cityExists['status']) return ['error'=>true,'message'=>'Please use only verified market cities or select the nearest city to you!','field'=>'city'];
      return ["status"=>true];
    }

    private function getMtraderData($tradeId)
    {
      $this->load->model('trader');
      $this->load->model('users');
      $userExists = $this->model_users->getUserbyTradeId($tradeId);
      if(!$userExists['status']) return [ "error"=>true, "message"=>"Invalid User"];
      return $userExists;
    }

    public function addCart($tradeId,$productId,$quantity = 1)
    {
      $this->load->model('trader');
      $userExists = $this->model_trader->getMTraderByTradeId($tradeId);
      if(!$userExists['status']) return [ "error"=>true, "message"=>"Invalid User"];
      $insert = $this->model_trader->addCart($userExists['data']['mtraderid'],$productId,$quantity);
      return ["status"=>true,"cartid"=>$insert,"message"=>"Item successfully added"];
    }
    
    public function getTraderCart($tradeId)
    {
      $this->load->model('trader');
      $this->load->model('products');
      $userExists = $this->model_trader->getMTraderByTradeId($tradeId);
      if(!$userExists['status']) return [ "error"=>true, "message"=>"Invalid User"];
      $cartExists = $this->model_trader->getTraderCart($userExists['data']['mtraderid']);
      $cartData = [];
      if($cartExists['status']) $cartData = $cartExists['data'];
      foreach($cartData as $cartItem){
        $this->model_products->getProduct($cartItem['product_id']);
      }
      return ["status"=>true,"cart"=>$cart];

    }

    public function getCartItem($cartId)
    {
      $this->load->model('trader');
      $this->load->model('products');
      $cartExists = $this->model_trader->getcartData($cartId);
      if(!$cartExists['status']) return [ "error"=>true, "message"=>"Item not found or no longer available"];
      $cartProduct = array();
      //$ $this->model_products->getProduct($cartExists['data']['product_id']);
      $cartProduct['quantity'] = $cartExists['data']['quantity'];
      return ["status"=>true,"item"=>$cartProduct];
    }

    public function updateCart($cartId,$quantity)
    {
      $this->load->model('trader');
      $cartExists = $this->model_trader->getcartData($cartId);
      if(!$cartExists['status']) return [ "error"=>true, "message"=>"Item not found or no longer available"];
      $update = $this->model_trader->updateCart($cartId,$quantity);
      return ["status"=>true,"message"=>"Item updated"];
    }

    public function removeCartItem($cartId)
    {
      $this->load->model('trader');
      $cartExists = $this->model_trader->getcartData($cartId);
      if(!$cartExists['status']) return [ "error"=>true, "message"=>"Item not found or no longer available"];
      $update = $this->model_trader->updateCartStatus($cartId);
      return ["status"=>true,"message"=>"Item removed from cart"];
    }
  }
?>