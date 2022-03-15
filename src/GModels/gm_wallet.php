<?php 
 	class GMWallet extends GModel{ 

		private $mainwallettbl = ''; 
		
		public function addWalletTransaction($userId,$credit,$debit,$description,$reference)
		{
			return $this->db->insert($this->mainwallettbl,
        array(
          'user_id' =>$userId,
          'credit'=>$credit,
          'debit'=>$debit,
					'description'=>$description,
					'date'=>date("Y-m-d"),
					'time'=>date("H:i:s"),
					'modifiedat'=>date("Y-m-d H:i:s"),
          'reference'=>$reference
        )
      ); 
		}

		public function updateTransaction($data,$condition = [])
		{
			return $this->db->update($this->mainwallettbl,$data,$condition);
		}

		private function addFilters($filters)
		{

			if(isset($filter['userid']) && strlen($filter['userid']) > 0) $this->db->and_where('user_id',$filter['userid'],$this->mainwallettbl);
			if(isset($filter['usergroupid']) && strlen($filter['usergroupid']) > 0) $this->db->and_where('usergroup_id',$filter['usergroupid'],$this->mainwallettbl);
			if(isset($filter['credit']) && strlen($filter['credit']) > 0) $this->db->and_where('credit',$filter['credit'],$this->mainwallettbl);
			if(isset($filter['debit']) && strlen($filter['debit']) > 0) $this->db->and_where('debit',$filter['debit'],$this->mainwallettbl);

			if(isset($filter['status']) && strlen($filter['status']) > 0) $this->db->and_where('status',$filter['status'],$this->mainwallettbl);
			if(isset($filter['channel']) && strlen($filter['channel']) > 0) $this->db->and_where('channel',$filter['channel'],$this->mainwallettbl);
			if(isset($filter['meta']) && strlen($filter['meta']) > 0) $this->db->and_where('meta',$filter['meta'],$this->mainwallettbl);

			if(isset($filter['amount']) && strlen($filter['amount']) > 0) $this->db->add_condition('AND debit = ? OR credit = ?',[$filter['amount'],$filter['amount']]);

			if(isset($filter['startamount']) && strlen($filter['startamount']) > 0) $this->db->add_condition('AND debit >= ? OR credit >= ?',[$filter['startamount'],$filter['startamount']]);
			if(isset($filter['endamount']) && strlen($filter['endamount']) > 0) $this->db->add_condition('AND debit <= ? OR credit <= ?',[$filter['endamount'],$filter['endamount']]);

			if(isset($filter['description']) && strlen($filter['description']) > 0) $this->db->add_condition('AND description LIKE ?',['%'.$filter['description'].'%']);
			if(isset($filter['search']) && strlen($filter['search']) > 0) $this->db->add_condition('AND description LIKE ? OR debit LIKE ? OR credit LIKE ?',['%'.$filter['search'].'%','%'.$filter['search'].'%','%'.$filter['search'].'%']);

			if(isset($filter['on']) && strlen($filter['on']) > 0) $this->db->and_where('createdat',$filter['on'],$this->mainwallettbl);
			
			if(isset($filter['startdate']) && strlen($filter['startdate']) > 0) $this->db->and_where_less_equal('date',$filter['startdate'],$this->mainwallettbl);
			if(isset($filter['enddate']) && strlen($filter['enddate']) > 0) $this->db->and_where_greater_equal('date',$filter['enddate'],$this->mainwallettbl);
			if(isset($filter['starttime']) && strlen($filter['starttime']) > 0) $this->db->and_where_greater_equal('time',$filter['starttime'],$this->mainwallettbl);
			if(isset($filter['endtime']) && strlen($filter['endtime']) > 0) $this->db->and_where_greater_equal('time',$filter['endtime'],$this->mainwallettbl);
			
			if(isset($filter['startupdatedate']) && strlen($filter['startupdatedate']) > 0) $this->db->and_where_less_equal('modifiedat',$filter['startupdatedate'],$this->mainwallettbl);
			if(isset($filter['endupdatedate']) && strlen($filter['endupdatedate']) > 0) $this->db->and_where_greater_equal('modifiedat',$filter['endupdatedate'],$this->mainwallettbl);

			if(isset($filter['updateat']) && strlen($filter['updateat']) > 0) $this->db->and_where('modifiedat',$filter['updateat'],$this->mainwallettbl);

			if(isset($filter['limit'])){
				$limit = $filter['limit'];
				$this->db->limit($filter['limit']);
			}else{
				$limit = 20;
				$this->db->limit(20);
			}

			if(isset($filter['pageno'])  && strlen($filter['pageno'] > 0 )){
				$this->db->offset(($filter['pageno'] - 1 ) * $limit);
			}else $this->db->offset(0);

			if(isset($filter['sort'])){
				$sort = $filter['sort'];
			}else $sort = 'createdat';

			if(!isset($filter['order']) && strlen($filter['order'] > 0 )){
				$order =  $filter['order'];
			}else $order = 'DESC';

			if($order == 'DESC'){
				$this->db->order_by_desc($this->mainwallettbl,$sort);
			}else $this->db->order_by_asc($this->mainwallettbl,$sort);

			return $this->db;
		}

		public function getTransactions()
		{
			return $this->db->query( $this->mainusertbl, ['user_id AS userid','debit','credit','date','time','description','channel','meta','remarks','modifiedat','h_field AS hfield']);
		}

		public function searchTransactions($filter = [],$status = 1)
		{
			$filter['status'] = $status; 
			$this->getTransactions();
			return $this->addFilters($filter)->exec()->rows;
		}

		public function getUserWalletBalance($userId,$status = 1 )
		{
			$result =  $this->db->query($this->mainwallettbl,['SUM(credit) AS credit', 'SUM(debit) AS debit '])->where_equal('user_id',$userId)->and_where('status',$status)->exec()->row;

			return (float) $result['credit'] - (float) $result['debit'];
		}
 	} 
 ?> 

 domain name