<?php 
  class GCOrders Extends GController{
    public function newOrder($traderId,$deliveryId,$coupon = 0)
    {
      //coupon validations
      $this->load->model('trader');
      $this->load->model('checkout');
      $userExists = $this->model_trader->getTraderByTradeId($tradeId);
      if(!$userExists['status'])return ["error"=>true,"message"=>"User not found"];
      $hasCart = $this->getTraderCart($userExists['data']['mtraderid']);
      if(!$hasCart['status']) return ["error"=>true,"message"=>"No cart Item Found!"];



      $this->model_checkout->getTraderByTradeId($tradeId);
      $cartTotal = $this->model_checkout->getCartTotal($userExists['data']['mtraderid']);
      $cartTotal = $cartTotal['status'] ? $cartTotal['data']['total'] : 0;
      $addCheckout = $this->model_checkout->addCheckout($cartTotal);
      foreach($hasCart['data'] as $item){
        $this->model_checkout->addOrder($addCheckout['insertId'],$item['orderid'],$item['product_id'],$item['quantity'],$item['amount'],$userExists['data']['mtraderid']);
      }
    }

  }
?>