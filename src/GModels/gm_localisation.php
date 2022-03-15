<?php 
  class GMLocalisation Extends GModel{

    public function addCountry($name,$code,$currencyId,$weightId)
    {
      return $this->db->insert('country',['name'=>$name,'code'=>$code,'currency_id'=>$currencyId,'weight_id'=>$weightId]);
    }

    public function addRegion($name,$state,$countryId)
    {
      return $this->db->insert('city',['name'=>$name,'state'=>$state,'country_id'=>$countryId]);
    }

    public function addCurrency($name,$code,$entitycode,$rate,$iso)
    {
      return $this->db->insert('currency',['name'=>$name,'code'=>$code,'entitycode'=>$entitycode,'rate'=>$rate,'iso'=>$iso]);
    }

    public function addManufacturer($name)
    {
      return $this->db->insert('manufacturer',['name'=>$name]);
    }

    public function updateCountry($countryId,$name,$code,$currencyId,$weightId)
    {
      return $this->db->update('country',['name'=>$name,'code'=>$code,'currency_id'=>$currencyId,'weight_id'=>$weightId],["id"=>$countryId]);
    }

    public function updateRegion($regionId,$name,$state,$countryId)
    {
      return $this->db->update('city',['name'=>$name,'state'=>$state,'country_id'=>$countryId],["id"=>$regionId]);
    }

    public function updateCurrency($currencyId,$name,$code,$entitycode,$rate,$iso)
    {
      return $this->db->update('currency',['name'=>$name,'code'=>$code,'entitycode'=>$entitycode,'rate'=>$rate,'iso'=>$iso],["id"=>$currencyId]);
    }

    public function updateCurrencyRate($currencyId,$rate)
    {
      return $this->db->update('currency',['rate'=>$rate],["id"=>$currencyId]);
    }

    public function updateCountryStatus($countryId,$status = 0)
    {
      return $this->db->update('country',['status'=>$status],["id"=>$currencyId]);
    }

    public function updateRegionStatus($regionId,$status = 0)
    {
      return $this->db->update('city',['status'=>$status],["id"=>$regionId]);
    }

    public function updateCurrencyStatus($currencyId,$status = 0)
    {
      return $this->db->update('currency',['status'=>$status],["id"=>$currencyId]);
    }

    private function getallcurrency(){
      return $this->db->query('currency',['name','code','iso','rate','entity']);
    }

    private function getallCountries()
    {
      return $this->db->query('country',['country.id AS countryid','country.name','country.code AS countrycode','telcode','currency.name AS currency','entitycode','iso AS currencyiso','rate AS currencyrate','weight.name AS weightunit','currency.code AS currencycode'])->join('left',[['table'=>'currency','field'=>'id'],['table'=>'country','field'=>'id']])->join('left',[['table'=>'weight','field'=>'id'],['table'=>'country','field'=>'weight_id']]);;
    }

    private function getregions()
    {
      return $this->db->query('city',['city.id AS cityid','country_id AS countryid','city.name AS cityname','country.name AS countryname', 'currency.name AS currency','currency.rate AS currencyrate', 'iso AS currencyiso','telcode', 'weight.name AS weightunit'])->join('inner',[['table'=>'country','field'=>'id'],['table'=>'city','field'=>'country_id']])->join('left',[['table'=>'currency','field'=>'id'],['table'=>'country','field'=>'id']])->join('left',[['table'=>'weight','field'=>'id'],['table'=>'country','field'=>'weight_id']]);
    }
    public function getCountries($status = 1)
    {
      return $this->getallCountries()->where_equal('country.status',$status)->exec()->rows;
    }

    public function getCountryRegions($countryId,$status = 1)
    {
      return $this->getregions()->where_equal('city.status',$status)->and_where('country_id',$countryId,'city')->exec()->rows;
    }
    public function getCountryStates($countryId,$status = 1)
    {
      return $this->db->query('city',['DISTINCT city.state'])->where_equal('city.status',$status)->and_where('country_id',$countryId,'city')->exec()->rows;
    }

    public function getRegion($regionId,$status = 1)
    {
      return $this->getregions()->where_equal('city.status',$status)->and_where('city.id',$regionId)->exec()->row;
    }
  }
?>