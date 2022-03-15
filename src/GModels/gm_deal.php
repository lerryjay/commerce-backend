<?php 
  class GMDeals Extends GModel{
    public function addDeal($mtraderid,$productId,$amount,$date )
    {
      return $this->db->insert('deals',
        [
          'product_id'=>$productId,
          'mtrader_id'=>$mtraderid,
          'amount'=>$amount,
          'date'=>$date
        ]
      );
    }

    private function addCoupon($usertype,$code,$usertypeid,$valuetype,$value,$expdate,$maxuse)
    {
      return $this->db->insert('coupons',
        [
          'code' => $code,
          'usertype'=>$usertype,
          'usertype_id'=>$usertypeid,
          'valuetype'=>$valuetype,
          'value'=>$value,
          'expdate'=>$expdate,
          'maxuse'=>$maxuse
        ]
      );
    }
    public function addMarketCoupon($valuetype,$value,$expdate,$maxuse = 100)
    {
      return $this->addCoupon(1,0,$code,$valuetype,$value,$expdate,$maxuse);
    }
    public function addSellerCoupon($msellerid,$valuetype,$value,$expdate,$maxuse = 100)
    {
      return $this->addCoupon('2',$msellerid,$code,$valuetype,$value,$expdate,$maxuse);
    }
    public function addCourierCoupon($mcourier,$valuetype,$value,$expdate,$maxuse = 100)
    {
      return $this->addCoupon('3',$mcourier,$code,$valuetype,$value,$expdate,$maxuse);
    }

  }