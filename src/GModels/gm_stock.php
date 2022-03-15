<?php 
 	class GMStock extends GModel{ 

		private $mainstocktbl = ''; 
		 
		public function addStock($productId,$quantity,$unit,$userId,$storeId,$location,$costPrice,$supplierId,$stockStatus,$salesPrice,$wholesalesprice,$wholeSalesUnit)
		{
			$this->db->insert('stockinventory',[
				'product_id'=>$productId,
				'quantity'=>$quantity,
				'unit'=>$unit,
				'costprice'=>$costPrice,
				'supplier_id'=>$supplierId,
				
				'stockstatus'=>$stockStatus,
				'retailprice'=>$salesPrice,
				'wholesalesprice'=>$wholesalesprice,
				'wholesalesunit'=>$wholeSalesUnit,
				
				'user_id'=>$userId,
				'location'=>$location,
				'store_id'=>$storeId,
				'createdat'=>date('Y-m-d H:i:s'),
				'modifiedat'=>date('Y-m-d H:i:s')
			]);
		}


		public function addStockItems($batchNo,$expiryDate,$identificationNumber,$condition,$stockinventoryId,$stockStatus)
		{
			$this->db->insert('stockItems',['batchnumber'=>$batchNo,'expirydate'=>$expiryDate,'gtin'=>$identificationNumber,'condition'=>$condition,'stockinventory_id'=>$stockinventoryId,'stockstatus'=>$stockStatus,'createdat'=>date('Y-m-d H:i:s'),	'modifiedat'=>date('Y-m-d H:i:s')]);
		}

		public function addRefund($recipientUserId,$invoiceNo,$identificationNumber,$productId,$cost,$reason,$desription,$refundNature,$quantity)
		{
			$this->db->insert('refunds',[
				'invoice_no'=>$invoiceNo,
				'gtin'			=>$identificationNumber,
				'product_id'=>$productId,
				'cost'=>$cost,
				'fault'=>$fault,
				'desription'=>$desription,
				'refundnature'=>$refundNature,
				'quantity'=>$quantity,
				'recipientUser_id'=>$recipientUserId,
				'createdat'=>date('Y-m-d H:i:s'),
				'modifiedat'=>date('Y-m-d H:i:s')
			]);
		}

		public function getStockInventory()
		{
			return $this->db->query('');
		}
		
		public function getStockItems()
		{
			return $this->db->query('');
		}
		
		
		
		public function getRefunds()
		{
			return $this->db->query('');

		}

		private function filterRefunds($filter)
		{
			if(isset($filter['recipientUserId']) && strlen($filter['recipientUserId']) > 0) $this->db->and_where('user_id',$filter['recipientUserId'],$this->mainwallettbl);
			if(isset($filter['usergroupid']) && strlen($filter['usergroupid']) > 0) $this->db->and_where('usergroup_id',$filter['usergroupid'],$this->mainwallettbl);

			if(isset($filter['cost']) && strlen($filter['cost']) > 0) $this->db->and_where('credit',$filter['cost'],$this->mainwallettbl);

			if(isset($filter['startcost']) && strlen($filter['startcost']) > 0) $this->db->add_condition('AND cost >= ?',[$filter['startcost']]);
			if(isset($filter['endcost']) && strlen($filter['endcost']) > 0) $this->db->add_condition('AND cost <= ?',[$filter['endcost']]);

			if(isset($filter['fault']) && strlen($filter['fault']) > 0) $this->db->and_where('fault',$filter['fault'],$this->mainwallettbl);
			if(isset($filter['refundnature']) && strlen($filter['refundnature']) > 0) $this->db->and_where('refundnature',$filter['refundnature'],$this->mainwallettbl);
			if(isset($filter['description']) && strlen($filter['description']) > 0) $this->db->add_condition('AND description LIKE ?',['%'.$filter['description'].'%']);

			if(isset($filter['description']) && strlen($filter['description']) > 0) $this->db->add_condition('AND description LIKE ?',['%'.$filter['description'].'%']);
			if(isset($filter['description']) && strlen($filter['description']) > 0) $this->db->add_condition('AND description LIKE ?',['%'.$filter['description'].'%']);

			if(isset($filter['createdat']) && strlen($filter['createdat']) > 0) $this->db->and_where('createdat',$filter['updateat'],$this->mainwallettbl);
			if(isset($filter['updateat']) && strlen($filter['updateat']) > 0) $this->db->and_where('modifiedat',$filter['updateat'],$this->mainwallettbl);

			if(isset($filter['startdate']) && strlen($filter['startdate']) > 0) $this->db->and_where_less_equal('createdat',$filter['startdate'],$this->mainwallettbl);
			if(isset($filter['enddate']) && strlen($filter['enddate']) > 0) $this->db->and_where_greater_equal('createdat',$filter['enddate'],$this->mainwallettbl);


			if(isset($filter['modifledstartdate']) && strlen($filter['modifledstartdate']) > 0) $this->db->and_where_less_equal('modifiedat',$filter['modifledstartdate'],$this->mainwallettbl);
			if(isset($filter['modifiedenddate']) && strlen($filter['modifiedenddate']) > 0) $this->db->and_where_greater_equal('modifiedat',$filter['modifiedenddate'],$this->mainwallettbl);

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

		public function searchRefunds($filters = [], $status = 1)
		{
			$this->getRefunds('status',$status);
			return $this->filterRefunds($filter)->exec()->rows;
		}

		public function filterStockInventory($filter)
		{
			if(isset($filter['productid']) && strlen($filter['productid']) > 0) $this->db->and_where('product_id',$filter['productid'],$thisquantitymainwallettbl);
			if(isset($filter['unit']) && strlen($filter['unit']) > 0) $this->db->and_where('unit',$filter['unit'],$thiscostpricemainwallettbl);
			if(isset($filter['supplierid']) && strlen($filter['supplierid']) > 0) $this->db->and_where('supplier_id',$filter['supplierid'],$thisstockstatusmainwallettbl);
			if(isset($filter['retailprice']) && strlen($filter['retailprice']) > 0) $this->db->and_where('retailprice',$filter['retailprice'],$thiswholesalespricemainwallettbl);
			if(isset($filter['wholesalesunit']) && strlen($filter['wholesalesunit']) > 0) $this->db->and_where('wholesalesunit',$filter['wholesalesunit'],$thisuser_idmainwallettbl);
			if(isset($filter['location']) && strlen($filter['location']) > 0) $this->db->and_where('location',$filter['location'],$thisstore_idmainwallettbl);
			if(isset($filter['storeid']) && strlen($filter['storeid']) > 0) $this->db->and_where('store_id',$filter['storeid'],$this->mainwallettbl);

			if(isset($filter['quantity']) && strlen($filter['quantity']) > 0) $this->db->and_where('quantity',$filter['quantity'],$this->mainwallettbl);
			if(isset($filter['costprice']) && strlen($filter['costprice']) > 0) $this->db->and_where('costprice',$filter['costprice'],$this->mainwallettbl);
			if(isset($filter['stockstatus']) && strlen($filter['stockstatus']) > 0) $this->db->and_where('stockstatus',$filter['stockstatus'],$this->mainwallettbl);
			if(isset($filter['wholesalesprice']) && strlen($filter['wholesalesprice']) > 0) $this->db->and_where('wholesalesprice',$filter['wholesalesprice'],$this->mainwallettbl);
			if(isset($filter['user_id']) && strlen($filter['user_id']) > 0) $this->db->and_where('user_id',$filter['user_id'],$this->mainwallettbl);
			
			if(isset($filter['createdat']) && strlen($filter['createdat']) > 0) $this->db->and_where('createdat',$filter['updateat'],$this->mainwallettbl);

			if(isset($filter['createdat']) && strlen($filter['createdat']) > 0) $this->db->and_where('createdat',$filter['updateat'],$this->mainwallettbl);
			if(isset($filter['updateat']) && strlen($filter['updateat']) > 0) $this->db->and_where('modifiedat',$filter['updateat'],$this->mainwallettbl);

			if(isset($filter['startdate']) && strlen($filter['startdate']) > 0) $this->db->and_where_less_equal('createdat',$filter['startdate'],$this->mainwallettbl);
			if(isset($filter['enddate']) && strlen($filter['enddate']) > 0) $this->db->and_where_greater_equal('createdat',$filter['enddate'],$this->mainwallettbl);


			if(isset($filter['modifledstartdate']) && strlen($filter['modifledstartdate']) > 0) $this->db->and_where_less_equal('modifiedat',$filter['modifledstartdate'],$this->mainwallettbl);
			if(isset($filter['modifiedenddate']) && strlen($filter['modifiedenddate']) > 0) $this->db->and_where_greater_equal('modifiedat',$filter['modifiedenddate'],$this->mainwallettbl);

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

		public function searchStockInventory($filter = [], $status = 1)
		{
			$this->getStockInventory()->where_equal('status',$status);
			return $this->filterStockInventory($filter)->exec()->rows;
		}

		public function filterStockItems($filter)
		{
			
			if(isset($filter['productid']) && strlen($filter['productid']) > 0) $this->db->and_where('stockinventory.product_id',$filter['productid'],$this->mainwallettbl);
			
			if(isset($filter['batchnumber']) && strlen($filter['batchnumber']) > 0) $this->db->and_where('batchnumber',$filter['batchnumber'],$this->mainwallettbl);
			if(isset($filter['expirydate']) && strlen($filter['expirydate']) > 0) $this->db->and_where('expirydate',$filter['expirydate'],$this->mainwallettbl);
			if(isset($filter['gtin']) && strlen($filter['gtin']) > 0) $this->db->and_where('gtin',$filter['gtin'],$this->mainwallettbl);
			if(isset($filter['condition']) && strlen($filter['condition']) > 0) $this->db->and_where('condition',$filter['condition'],$this->mainwallettbl);
			if(isset($filter['stockinventoryid']) && strlen($filter['stockinventoryid']) > 0) $this->db->and_where('stockinventory_id',$filter['stockinventoryid'],$this->mainwallettbl);
			if(isset($filter['stockstatus']) && strlen($filter['stockstatus']) > 0) $this->db->and_where('stockstatus',$filter['stockstatus'],$this->mainwallettbl);
			
			
			if(isset($filter['createdat']) && strlen($filter['createdat']) > 0) $this->db->and_where('createdat',$filter['on'],$this->mainwallettbl);
			if(isset($filter['updateat']) && strlen($filter['updateat']) > 0) $this->db->and_where('modifiedat',$filter['updateat'],$this->mainwallettbl);

			if(isset($filter['startdate']) && strlen($filter['startdate']) > 0) $this->db->and_where_less_equal('createdat',$filter['startdate'],$this->mainwallettbl);
			if(isset($filter['enddate']) && strlen($filter['enddate']) > 0) $this->db->and_where_greater_equal('createdat',$filter['enddate'],$this->mainwallettbl);

			if(isset($filter['modifledstartdate']) && strlen($filter['modifledstartdate']) > 0) $this->db->and_where_less_equal('modifiedat',$filter['modifledstartdate'],$this->mainwallettbl);
			if(isset($filter['modifiedenddate']) && strlen($filter['modifiedenddate']) > 0) $this->db->and_where_greater_equal('modifiedat',$filter['modifiedenddate'],$this->mainwallettbl);
			
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

		public function searchStockItems($filter = [],$status = 1)
		{
			$this->getStockItems()->join('inner',[['table'=>'stockitems','field'=>'stockinventory_id'],['table'=>'stockinventory','field'=>'id']])->where_equal('status',$status);
			return $this->filterStockItems($filter)->exec()->rows;
		}
 	} 
 ?> 