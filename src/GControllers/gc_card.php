<?php 
	class GCCard extends GController{
		public function index()
		{ 
			$this->load->model('digitalproduct');
			$cards = $this->model_digitalproduct->getDigitalProductsByType('card');
			if($cards['status']) $this->http->emit(['status'=>true,'message'=>'GiftCard successfully retrieved!','data'=>$cards['data']]);
			else  $this->http->emit(['status'=>true,'message'=>'No cards available or this service is not supported in your region!', 'data'=>[]]);
		} 
		/** 
		 * @param HTTPPOST $name 		- Name of the card
		 * @param HTTPPOST $buy 		- buy rate of the card
		 * @param HTTPPOST $sell 		- sell rate of the card
		 * @param HTTPPOST $selling - boolean selling card
		 * @param HTTPPOST $buying  - boolean buying card
		 * @return HTTPRESPONSE - status boolean, message - String translation of status  
		 * */	
		public function add()
		{
			extract($this->validateAddGiftCard());
			$this->load->model('product');
			$addProduct = $this->model_product->addProduct('0',$name,'0',0,$description,1,2);
			if($addProduct)
			{	
				$this->load->model('digitalproduct');
				$add = $this->model_digitalproduct->addDigitalProduct($addProduct,'card',(float)$buy,(float)$sell,(int)$buying,(int)$selling);
				if($add) $this->http->emit(['status'=>true,'message'=>'GiftCard successfully added!', ]);
				else  $this->http->emit(['status'=>false,'message'=>'Error adding card!']);
			}
		}

		private function validateAddGiftCard()
		{
			extract($this->http->post(['name','buy','sell','buying','selling']));

			$this->load->library('validator');
			if(!$this->library_validator->dirtyString($name,1,200)){
				$this->http->emit(['status'=>false,'message'=>'Please enter card name ','data'=>['field'=>'name']]);
			}

			$buying ??= false;
			if(!$this->library_validator->amount($buy) && $buying){
				$this->http->emit(['status'=>false,'message'=>'Invalid buying amount','data'=>['field'=>'buying']]);
			}else{
				$buy ??= 0;
			}

			$selling ??= false;
			if(!$this->library_validator->amount($sell) && $selling){
				$this->http->emit(['status'=>false,'message'=>'Invalid selling amount','data'=>['field'=>'selling']]);
			}else{
				$sell ??= 0;
			}

			if(isset($description) && !$this->library_validator->dirtyString($description,1,300)){
				$this->http->emit(['status'=>false,'message'=>'Description cannot be longer than 300 characters','data'=>['field'=>'description']]);
			}else $description ??='';

			return ['name'=>$name,'buy'=>$buy,'sell'=>$sell,'buying'=>(bool)$buying,'selling'=>(bool)$selling, 'description'=>$description];
		}

		/**
		 * undocumented function summary
		 *
		 * Undocumented function long description
		 *
		 * @param Type $var Description
		 * @return type
		 * @throws conditon
		 **/
		public function card()
		{
			extract($_GET);
			$productId = isset($productid) ? $productid : '';
			$this->load->model('digitalproduct');
			$itemExist = $this->model_digitalproduct->getDigitalProduct($productId);
			if($itemExist['status']) $this->http->emit(['status'=>true,'message'=>'GiftCard successfully retrieved', 'data'=>$itemExist['data']]);
			$this->http->emit(['status'=>false,'message'=>'GiftCard not found!','code'=>404]);
		}

		public function update()
		{
			$data = $this->validateGiftCardUpdate();
			extract($data);
			$this->load->model('product');
			$productExists = $this->model_product->getProductsById($productId);
			$product = $productExists['data'];
			$this->model_product->update($productId,['name'=>$name]);
			$update = $this->model_digitalproduct->update($productId,'card',(float)$buy,(float)$sell,(int)$buying,(int)$selling);
			if($update) $this->http->emit(['status'=>true,'message'=>'GiftCard successfully updated']);
			$this->http->emit(['status'=>false,'message'=>'GiftCard update failed!']);
		}

		private function validateGiftCardUpdate()
		{
			$this->load->library('validator');
			extract($this->http->post(['name','buy','sell','buying','selling','productid']));
			if(!$this->library_validator->dirtyString($name,1,200)){
				$this->http->emit(['status'=>false,'message'=>'Please enter card name ','data'=>['field'=>'nmae']]);
			}
			
			$buying ??= false;
			if(!$this->library_validator->amount($buy) && $buying){
				$this->http->emit(['status'=>false,'message'=>'Invalid buying amount','data'=>['field'=>'buying']]);
			}else{
				$buy ??= 0;
			}

			$selling ??= false;
			if(!$this->library_validator->amount($sell) && $selling){
				$this->http->emit(['status'=>false,'message'=>'Invalid selling amount','data'=>['field'=>'selling']]);
			}else{
				$sell ??= 0;
			}

			$this->load->model('digitalproduct');
			$productId = isset($productid) ? $productid : '';
			$itemExist = $this->model_digitalproduct->getDigitalProduct($productId);
			if(!$itemExist['status'])$this->http->emit(['status'=>false,'message'=>'Invalid productid','data'=>['field'=>'productid'],'code'=>404]);
			return ['name'=>$name,'buy'=>$buy,'sell'=>$sell,'buying'=>(bool)$buying,'selling'=>(bool)$selling,'product'=>$itemExist['data'], 'productId'=>$productId];
		}
 	} 
 ?> 