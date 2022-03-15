<?php 
  class GMCouriers Extends GModel{
    private $maintbl = 'mcouriers';
  	public function addCourier($userId,$companyname,$slogan,$address,$city,$country,$maxweight,$note,$businessRegisteration,$date)
  	{
      return $this->db->insert($this->maintbl,
        [
          'userid'=>$userId,
          'name' => $companyname,
          'address'=>$address,
          'city_id'=>$city,
          'country_id'=>$country,
          'slogan'=>$slogan,
          'maxweight'=>$maxweight,
          'note'=>$note,
          'businessregisteration'=>$businessRegisteration,
          'registration_date'=>$date
        ]
      );
    }
    
    public function addCourierRegion($mcourierId,$countryId,$regionId)
    {
      return $this->db->insert('mcourierregion',
        [
          'mcourier_id' => $mcourierId,
          'country_id' => $countryId,
          'region_id' => $regionId
        ]
      );
    }

    public function updateCourier($mcourierId,$companyname,$slogan,$address,$city,$country,$maxweight,$note,$businessRegisteration,$date){
      return $this->db->update($this->maintbl,
        [
          'name' => $companyname,
          'address'=>$address,
          'city_id'=>$city,
          'country_id'=>$country,
          'slogan'=>$slogan,
          'maxweight'=>$maxweight,
          'note'=>$note,
          'businessregisteration'=>$businessRegisteration,
          'registration_date'=>$date
        ],
        [
          'id'=>$mcourierId
        ]
      );
    }
    public function updateCourierRegionStatus($mcourierregionId,$status = 0)
    {
      return $this->db->update($this->maintbl,
        [
          'status' => $status
        ],
        [
          'id'=>$mcourierregionId
        ]
      );
    }

    public function updateCourierStatus($mcourierId,$status = 1)
    {
      return $this->db->update($this->maintbl,
        [
          
          'registration_date'=>$date
        ],
        [
          'id'=>$mcourierId
        ]
      );
    }

    private function getmcouriers()
    {
      return $this->db->query($this->maintbl,
        [
          'mcouriers.*',
          'country.name AS countryname',
          'country.currency AS currency', 
          'country.currency_iso AS currencyiso',
          'country.language AS language',
          'country.id AS country_id', 
          'country.weight AS weightunit',
          'city.name AS cityname', 
          'city.state AS citystate', 
          'city.id AS city_id'
        ]
      )->join('inner',[ ['field' => 'id',  'table' => 'city'], [   'field' => 'city_id', 'table' => 'mcouriers' ] ])->join('inner', [['field' => 'id','table' => 'country', ],  ['field' => 'country_id', 'table' => 'city' ]]);
    }

    public function getAllCourier($status = 1){
      $this->getmcouriers(); 
      $this->db->where_equal('mcouriers.status',1);
      return $this->db->execute();
    }

  	public function getCourierByName($name,$status = 1){
      $this->getmcouriers(); 
      $this->db->where_equal('mcouriers.status',1);
      $this->db->and_where_like('name','%'.$name.'%','mcouriers');
      return $this->db->execute();
      
  	}

    public function getCourierByCountry($countryId,$status = 1)
    {
      $this->getmcouriers(); 
      $this->db->where_equal('status',$status,'mcouriers');
      $this->db->and_where('country_id',$countryId,'mcouriers');
      return $this->db->execute();
    }
    public function getCouriersByCity($cityId,$status = 1)
    {
      $this->getmcouriers(); 
      $this->db->where_equal('status',$status,'mcouriers');
      $this->db->and_where('city_id',$cityId,'mcouriers');
      return $this->db->execute();
    }

    public function getCourierByRegDate($regDate,$status = 1)
    {
      $this->getmcouriers(); 
      $this->db->where_equal('status',$status,'mcouriers');
      $this->db->and_where('registration_date',$regDate,'mcouriers');
      return $this->db->execute();
    }
    public function getCourierProfile($courierId,$status = 1)
    {
      $this->getmcouriers(); 
      $this->db->where_equal('status',$status,'mcouriers');
      $this->db->and_where('id',$courierId,'mcouriers');
      return $this->db->execute();
    }
    public function getUserCourierProfile($userId,$status = 1)
    {
      $this->getmcouriers(); 
      $this->db->where_equal('status',$status,'mcouriers');
      $this->db->and_where('user_id',$userId,'mcouriers');
      return $this->db->execute();
    }

    private function getallcourierregions(Type $var = null)
    {
      return $this->db->query('mcourierregion',['mcourierregion.id AS mcourierregionid','mcourierregion.city_id','mcourierregion.country_id', 'country.name AS countryname','city.name AS cityname','country.weight AS weightunit','city.name AS cityname', 'city.state AS citystate',  ])->join('inner',[ ['field' => 'id',  'table' => 'city'], [   'field' => 'city_id', 'table' => 'mcourierregion' ] ])->join('inner', [['field' => 'id','table' => 'country', ],  ['field' => 'country_id', 'table' => 'city' ]]);
    }

    public function getCourierCoverageRegion($mcourierId,$status = 1)
    {
      return $this->getallcourierregions()->where_equal('status',$status,'mcourierregion')->and_where('mcourier_id',$mcourierId,'mcourierregion')->execute();
    }

    public function getRegionCourier($regionId,$status = 1)
    {
      return $this->getallcourierregions()->where_equal('status',$status,'mcourierregion')->and_where('region_id',$regionId,'mcourierregion')->execute();
    }

    public function getCourierCovering($region1Id,$region2Id,$status = 1)
    {
      return $this->getallcourierregions()->where_exists('SELECT * FROM mcourierregion WHERE region_id ',[$region1Id])->where_exists('SELECT * FROM mcourierregion WHERE region_id ',[$region2Id])->where_exists('SELECT * FROM mcourierregion WHERE region_id ',[$region1Id])->or_where_exists('SELECT * FROM mcourierregion WHERE city.region_id = ? AND status = ? AND region_id =  ? AND country_id =  mcourierregion.country_id',[0,$status,$region1Id])->and_where_exists('SELECT * FROM mcourierregion WHERE city.region_id = ?  AND region_id =  ? AND status = ?  AND country_id =  mcourierregion.country_id',[0,$status,$region2Id])->and_where('status',$status,'mcourierregion')->and_where('region_id',$regionId,'mcourierregion')->execute();
    }

    public function getCourierCoveringWeight($region1Id,$region2Id,$weight,$status = 1)
    {
      return $this->getallcourierregions()->where_exists('SELECT * FROM mcouriers WHERE maxweight <= ? AND status = ? ',[$weight,$status])->and_where_exists('SELECT * FROM mcourierregion WHERE region_id ',[$region1Id])->and_where_exists('SELECT * FROM mcourierregion WHERE region_id ',[$region2Id])->and_where_exists('SELECT * FROM mcourierregion WHERE region_id ',[$region1Id])->or_where_exists('SELECT * FROM mcourierregion WHERE city.region_id = ? AND status = ? AND region_id =  ? AND country_id =  mcourierregion.country_id',[0,$status,$region1Id])->and_where_exists('SELECT * FROM mcourierregion WHERE city.region_id = ?  AND region_id =  ? AND status = ?  AND country_id =  mcourierregion.country_id',[0,$status,$region2Id])->and_where('status',$status,'mcourierregion')->and_where('region_id',$regionId,'mcourierregion')->execute();
    }
  }
?>