<?php
	class GRProduct Extends GRoute{
		public function index(){
			echo 'worked';
    }
    
    public function addproduct()
    {
      $this->load->controller('products');
			$postFields = $this->http->post(['storeid','tradeid','name','visibility','type','quantity','price','shortdescription']);
      $res = $this->controller_products->addProducts($postFields['tradeid'],$postFields['name'],$postFields['price'],$postFields['quantity'],$postFields['shortdescription'],$postFields['type'],$postFields['visibility']);
			$this->http->emit($res);
    }

    public function updatecategories()
    {
      $this->load->controller('products');
			$postFields = $this->http->post(['productid','tradeid','categories']);
      $res = $this->controller_products->updateProductCategory($postFields['tradeid'],$postFields['productid'],json_decode($postFields['categories']));
			$this->http->emit($res);
    }

    public function updatesalesregion()
    {
      $this->load->controller('products');
      $postFields = $this->http->post(['productid','tradeid','regions']);
      $regions =  html_entity_decode($postFields['regions']);
      $res = $this->controller_products->updateSalesRegion($postFields['tradeid'],$postFields['productid'],json_decode($regions,true));
			$this->http->emit($res);
    }

    public function updatevisibility()
    {
      $this->load->controller('products');
      $data = $this->http->post(['tradeid','productid','visibility']);
      $res = $this->controller_products->updateSalesRegion($data['tradeid'],$data['productid'],$data['visibility']);
			$this->http->emit($res);
    }

    public function info()
    {
      $this->load->controller('products');
      $productid = $this->http->post('productid');
      $res = $this->controller_products->getProductInfoUser($productid);
			$this->http->emit($res);
    }
  
    

    public function categoryproducts()
    {
      $this->load->controller('products');
      $data = $this->http->post('categoryid');
      $res = $this->controller_products->getCategoryProducts($data);
			$this->http->emit($res);
    }

    public function sellerproducts()
    {
      $this->load->controller('products');
      $data = $this->http->post('productid');
      $res = $this->controller_products->getSellerProducts($data);
			$this->http->emit($res);
    }

    public function storeproducts()
    {
      $this->load->controller('products');
      $data = $this->http->post('storeid');
      $res = $this->controller_products->getStoreProducts($data);
			$this->http->emit($res);
    }

    public function addreview()
    {

    }

    public function likeproduct(Type $var = null)
    {
      # code...
    }


			
	}
?>