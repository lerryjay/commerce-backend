<?php 
 	 class GRGlobal extends GRoute{ 

 		public function index(){ 
 			//  echo 'worked';
        $this->load->library('encrypt');
        echo $this->library_encrypt->generate_api_private_key();
    } 
      
    public function countries()
    {
      $this->load->controller('global');
      $this->request->emit($this->controller_global->getCountries());
    }

    public function countryregions()
    {
      $this->load->controller('global');
      $this->request->emit($this->controller_global->getCountryRegions($this->request->get('countryid')));
    }
 	 } 
 ?> 