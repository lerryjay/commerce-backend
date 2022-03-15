<?php 
  class GMSections Extends GModel{
    public function addSection()
    {

    }

    public function addSectionRegion()
    {

    }

    public function getSections()
    {
      $this->load->model('sections');
      $this->model_sections->getAllRegionSections();
      $this->model_sections->getCountrySections();
      $this->model_sections->getRegionSections();
    }
  }
?>