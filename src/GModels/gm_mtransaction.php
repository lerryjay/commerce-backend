<?php 
  class GMMtransactions Extends GModel{
    private $transctbl = 'mtransactions';
    private $paymenttbl = 'payments';

    private function addPaymentOption()
    {
      
    }
    private function addMTransaction($mcourierId,$msellerId,$mtraderId,$credit,$debit,$description,$reference)
    {
      return $this->db->insert($this->maintransactionstbl,
        array(
          'mcourier_id' =>$mcourierId,
          'mseller_id' =>$msellerId,
          'mtrader_id'=>$mtraderId,
          'credit'=>$credit,
          'debit'=>$debit,
          'description'=>$description,
          'reference'=>$reference
        )
      ); //RECONSIDER TRANSACTION TYPES HIDDEN<OPEN
    }

    public function addTraderTransaction($mtraderId,$description,$reference,$credit=0,$debit=0)
    {
      return $this->addMTransaction(0,0,$mtraderId,$credit,$debit,$description,$reference);
    }
    public function addSellerTransaction($msellerId,$description,$reference,$credit=0,$debit=0)
    {
      return $this->addMTransaction(0,$msellerId,0,$credit,$debit,$description,$reference);
    }
    public function addCourierTransaction($mcourierId,$description,$reference,$credit=0,$debit=0)
    {
      return $this->addMTransaction($mcourierId,0,0,$credit,$debit,$description,$reference);
    }


    public function addPayment($option,$pstatus,$reference)
    {
      return $this->db->insert($this->paymenttbl,
        array(
          'option_id' =>$option,
          'pstatus' =>$pstatus,
          'reference'=>$reference
        )
      ); 
    }
    private function gettransactions($fields = [])
    {
      return $this->db->query($this->paymenttbl,array_merge($fields,['credit','debit','description','reference','date','time']));
    }

    public function getSellerTransactions($msellerId,$status = 1)
    {
      return $this->gettransactions()->where_equal('status',$status,'mtransactions')->and_where('mseller_id',$msellerId);
    }
    public function getCourierTransactions($mcourierId,$status = 1)
    {
      return $this->gettransactions()->where_equal('status',$status,'mtransactions')->and_where('mcourier_id',$mcourierId);
    }
    public function getTraderTransactions($mtraderId,$status = 1)
    {
      return $this->gettransactions()->where_equal('status',$status,'mtransactions')->and_where('mtrader_id',$mtraderId);  
    }
    
    public function getUserTransactions($userId,$status = 1)
    {
      
    }

    public function getTransactionByFilter($filter = array(),$status = 1)
    {
      return $this->gettransactions()->where_equal('status',$status,'mtransactions')->where_filter($filter);  
    }

    
  }
?>