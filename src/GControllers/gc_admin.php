<?php 
  class GCBanners Extends GController{

    public function addAds($name,$mediaurl,$mediatype,$owner,$inktype,$linkurl,$promorequestId )
    {
      # code...
    }
    private function validateAds()
    {
      $this->load->library("validator");
    }

	  public function getBanners($userRegionId){
      $this->load->model('banners');
      $this->model_banners->getBannersbyRegionDependence();
      $this->model_banners->getBannersbyRegionDependence();
	  }
  }
?>