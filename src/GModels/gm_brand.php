<?php

  class GMBrands extends GModel{
    private $manufactuerertablename = 'manufacturers';
    private $brandstable = 'brands';

    public function addManufacturer($name,$isin,$hqcountryId,$note = '')
    {
      return $this->db->insert($this->manufactuerertablename,
      array(
          'name'=>$name,
          'isin'=>$isin,
          'hqcountry_id'=>$hqcountryId,
          'note'=>$note
        )
      ); 
    }


    public function addManufacturerBrand($name,$manufacturerId,$note = '')
    {
      return $this->db->insert($this->brandstable,
      array(
          'name'=>$name,
          'manufacturer_id'=>$manufacturerId,
          'note'=>$note
        )
      ); 
    }

    public function addManufacturerCatgegory($manufacturerId,$categoryId)
    {
      return $this->db->insert($this->manufactuerertablename,
        array(
          'manufacturer_id'=>$manufacturerId,
          'category_id'=>$categoryId,
        )
      ); 
    }

    public function addManufacturerMedia( $manufacturerId,$mediaType,$mediaUrl,$mediaKey)
    {
      return $this->db->insert('manufacturermedia',
        array(
          'manufacturer_id'=>$mediaType,
          'mediatype'=>$mediaType,
          'mediaurl'=>$mediaUrl,
          'mediakey'=>$mediaKey,
        )
      ); 
    }

    private function getmanufacturers($fields = [])
    {
      return $this->db->query($this->manufactuerertablename,array_merge($fields,['manufacturers.name AS manufacturername','manufacturers.id AS manufacturerid' ]));
    }

    public function getManufacturersByCategory(){

    }


    public function listMarketManufacturers($status = 1)
    {
      $fields = ['(SELECT mediaurl AS logo FROM manufacturermedia WHERE mediakey = ? AND status = ? ) AS icon'];
      return $this->getmanufacturers($fields)->addParam(['LOGO_ICON',1])->where_equal('status',$status)->execute();
    }


    private function getbrands($fields = [])
    {
      return $this->db->query($this->brandstable,array_merge($fields,['brands.name AS brandname','brands.id AS brandid' ]));
    }

    public function getManufacturerBrands($manufacturerId,$status =1)
    {
      return $this->getbrands()->where_equal('brands.status',$status)->and_where('manufacturer_id',$manufacturerId,'brands')->execute();
    }
  }
?>
