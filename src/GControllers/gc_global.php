<?php 
  class GCGlobal Extends GController{
	  public function countries(){
      $this->load->model('localisation');
      $countries = $this->model_localisation->getCountries();
      if(!$countries['status']) $this->http->emit(array("error"=>true,"message"=>"Error fetching countries!"));
      $this->http->emit($countries);
    }

    public function country()
    {
      $this->load->model('localisation');
      $countryId = $this->http->get('countryid');
      $regions = $this->model_localisation->getCountryRegions($countryId);
      if(!$regions['status']) $this->http->emit(array("error"=>true,"message"=>"Error fetching regions!"));
      $this->http->emit($regions);
    }

    public function manufacturers(){
      $this->load->model('brands');
      $manufacturers = $this->model_brands->listMarketManufacturers();
      if(!$manufacturers['status']) $this->http->emit( ["error"=>true,"message"=>"Error fetching manufacturers!"]);
      $this->http->emit($manufacturers);
    }
    
    public function brands()
    {
      $this->load->model('brands');
      $manufacturerId = $this->http->get('manufacturerid');
      $brands = $this->model_brands->getManufacturerBrands($manufacturerId);
      if(!$brands['status']) $this->http->emit( ["error"=>true,"message"=>"Error fetching brands!"]);
      $this->http->emit($brands);
    }
  }
?>