<?php 
 	class GMDigitalproduct extends GModel{ 

		private $maindigitalproductstbl = 'productdigital'; 
		 

		/**
		 * undocumented function summary
		 *
		 * Undocumented function long description
		 *
		 * @param Type $var Description
		 * @return type
		 * @throws conditon
		 **/
		public function addDigitalProduct($productId,$type,$buy,$sell,$buying,$selling)
		{
			return $this->db->insert($this->maindigitalproductstbl,[
				'product_id'=>$productId,
				'type'=>$type,
				'buy'=>$buy,
				'sell'=>$sell,
				'selling'=>$selling,
				'buying'=>$buying
			]);
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
		public function update($productId,$type,$buy,$sell,$buying,$selling)
		{
			var_dump($productId,$type,$buy,$sell,$buying,$selling);
			return $this->db->update($this->maindigitalproductstbl,[
				'type'=>$type,
				'buy'=>$buy,
				'sell'=>$sell,
				'selling'=>$selling,
				'buying'=>$buying
			
			],['product_id'=>$productId]);
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
		private function getalldigitalproducts($fields = [])
		{
			return $this->db->query($this->maindigitalproductstbl,array_merge($fields,['buying ','selling','buy','sell','type','(SELECT name FROM products WHERE id = product_id) AS name']));
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
		public function getDigitalProduct($productId,$status = 1)
		{
			return $this->getalldigitalproducts()->where_equal('productdigital.status',$status)->and_where('product_id',$productId)->execute(1);
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
		public function getDigitalProductsByType($type,$status = 1)
		{
			return $this->getalldigitalproducts()->where_equal('productdigital.status',$status)->and_where('type',$type)->execute();
		}
 	} 
 ?> 