<?php 
  class GCTransactions Extends GController{

	  public function getUserBalance($userId){
	  	$db = $this->load->library('dbcrud');
	  	$db->read_query('g_bstransactions',array('IFNULL(SUM(credit- debit),0.00) AS balance'));
	  	$db->where_equal('user_id',$userId);
	  	$data = $db->execute(1);
	  	return $data['data']['balance'];
	  }

	  public function calculateCost($recipients,$senderId = ''){
	  	$recipients = explode(',', $recipients);
	  	$cost = count($recipients) * 2.5; //update script to dynamically modify values;
	  }

	  public function addTransaction($userId,$credit,$debit,$description){
	  	$db = $this->load->library('dbcrud');
	  	$insert = $db->insertTableRecord('g_bstransactions',['user_id'=>$userId,'credit'=>$credit,'debit'=>$description]);
	  	return $insert;
	  }
  }
?>