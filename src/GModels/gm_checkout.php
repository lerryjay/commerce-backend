<?php 
  class GMCheckout Extends GModel{
    private $maintbl = 'checkout';


    public function addCheckout($total,$couponId = 0,$date)
    {
      return $this->db->insert($this->maintbl,
        array(
          'total' => $total,
          'date'=>$date,
          'time'=>$time,
          'coupon_id'=>$couponId
        )
      );
    }

    public function addOrder($checkoutId,$orderId,$productId,$quantity,$amount,$mtraderId,$deliveryId = 0 ,$date )
    {
      return $this->db->insert('orders',
        array(
          'orderid' => $orderId,
          'product_id'=>$productId,
          'quantity'=>$quantity,
          'amount'=>$amount,
          'mtrader_id'=>$mtraderId,
          'checkout_id'=>$checkoutId,
          'delivery_id'=>$deliveryId,
          'date'=>$date
        )
      );
    }

    public function addDelivery($orderId,$addressId,$mtraderId,$date,$mcourierId)
    {
      return $this->db->insert('orders',
        array(
          'order_id' => $orderId,
          'address_id'=>$addressId,
          'mtrader_id'=>$mtraderId,
          'mcourier_id'=>$mcourierId,
          'dstatus'=>$dstatus,
          'date'=>$date
        )
      );
    }

    public function updateCheckout()
    {

    }

    
    public function updateOrder($orderId,$productId,$quantity,$amount,$mtraderId,$checkoutId,$deliveryId,$date)
    {
      return $this->db->insert('orders',
       [
          'product_id'=>$productId,
          'quantity'=>$quantity,
          'amount'=>$amount,
          'mtrader_id'=>$mtraderId,
          'checkout_id'=>$checkoutId,
          'delivery_id'=>$deliveryId,
       ],
       [
         'orderid'=>$orderId
       ]
      );
    }
    private function getallorders($fields=array())
    {
      return $this->db->query('orders',array_merge($fields,['orderid','product_id','quantity','amount','mtrader_id','chekout_id','delivery_id']));
    }

    public function getTraderOrders($mtraderId,$status =1)
    {
      return $this->getallorders()->where_equal('status',$status,'orders')->and_where('mtrader_id',$mtraderId)->execute();
    }
    
    public function getSellerOrders($msellerId,$status =1)
    {
      return $this->getallorders()->join('inner',[['table'=>'products','field'=>'id'],['table'=>'orders','field'=>'product_id']])->where_equal('status',$status,'orders')->and_where('mseller_id',$msellerId)->execute();
    }  

    public function getProductOrders($productId,$status =1)
    {
      return $this->getallorders()->join('inner',[['table'=>'products','field'=>'id'],['table'=>'orders','field'=>'product_id']])->where_equal('status',$status,'orders')->and_where('product_id',$productId)->execute();
    }

    public function getCheckoutOrders($checkoutId,$status =1)
    {
      return $this->getallorders()->join('inner',[['table'=>'products','field'=>'id'],['table'=>'orders','field'=>'product_id']])->where_equal('status',$status,'orders')->and_where('checkout_id',$checkoutId)->execute();
    }

    public function getOrderByStatus($orderStatus,$status)
    {
      return $this->getallorders()->join('inner',[['table'=>'products','field'=>'id'],['table'=>'orders','field'=>'product_id']])->where_equal('status',$status,'orders')->and_where('ostatus',$orderStatus)->execute();
    }

    public function getOrdersByFilter($filter = [],$status = 1)
    {
      return $this->getallorders()->join('inner',[['table'=>'products','field'=>'id'],['table'=>'orders','field'=>'product_id']])->where_equal('status',$status,'orders')->where_filter($filter)->and_where('checkout_id',$checkoutId)->execute();
    }

  }
?>