<?php 
  class GMBanners Extends GModel{
    private $mtransactiontbl = 'mtransactions';
 
    public function addBanner($mediatype,$mediaurl,$audience,$linktype,$linktypeid,$regionspecific,$usertypeid,$usertype,$expdate,$promorequestId)
    {
      return $this->db->insert('banners',
        array(
          'mediatype'=>$mediatype,
          'mediaurl'=>$mediaurl,
          'promorequest_id'=>$promorequestId,
          'audience'=>$audience,
          'linktype'=>$linktype,
          'linktypeid'=>$linktypeid,
          'regionspecific'=>$regionspecific,
          'usertype_id'=>$usertypeid,
          'owner'=>$usertype,
          'dateaddedd'=>date("Y-m-d"),
          "expdate"=>$expdate
        )
      ); 
    }

    public function addBannerRegion($bannerId,$countryId,$regionId)
    {
      return $this->db->insert('banners',
        array(
          'country_id'=>$countryId,
          'region_id'=>$regionId,
          'banner_id'=>$bannerId
        )
      ); 
    }

    public function updateBanner($bannerId,$mediatype,$mediaurl,$audience,$linktype,$linktypeid,$regionspecific,$usertypeid,$usertype,$expdate,$promorequestId)
    {
      return $this->db->update('banners',
        array(
          'mediatype'=>$mediatype,
          'mediaurl'=>$mediaurl,
          'promorequest_id'=>$promorequestId,
          'audience'=>$audience,
          'linktype'=>$linktype,
          'linktypeid'=>$linktypeid,
          'regionspecific'=>$regionspecific,
          'usertype_id'=>$usertypeid,
          'owner'=>$usertype,
          "expdate"=>$expdate
        ),
        [
          "id"=>$bannerId
        ]
      );
    }

    public function changeBannerStatus($bannerId,$status = 1)
    {
      return $this->db->update('banners',
        array(
          'status'=>$status
        ),
        [
          "id"=>$bannerId
        ]
      );
    }

    public function updateBannerRegionStatus($bannerRegionId,$status = 0)
    {
      return $this->db->update('bannerregion',
        array(
          'status'=>$status
        ),
        [
          "id"=>$bannerRegionId
        ]
      );
    }

    public function getBannerRegions($bannerId,$status = 1)
    {
      $this->db->query('bannerregion',['id','country_id','region_id']);
       $this->db->where_equal('banner_id',$bannerId);
       $this->and_where('status',$status);
       return $this->db->execute();
    }

    public function getBannerRegionId($bannerId,$regionId,$status = 1)
    {
       $this->db->query('bannerregion',['id','country_id','region_id']);
       $this->db->where_equal('banner_id',$bannerId);
       $this->and_where('region_id',$regionId);
       $this->and_where('status',$status);
       return $this->db->execute(1);
    }

    private function getbanners()
    {
      $this->db->query('banners',
        [
          'mediatype',
          'mediaurl',
          'promorequest_id',
          'audience',
          'linktype',
          'linktypeid',
          'regionspecific',
          'usertype_id',
          'owner'        
        ]
      );
    }
    public function getBannerById($bannerId,$status = 1)
    {
      $this->getbanners();
      $this->db->where_equal('id',$bannerId);
      $this->and_where('status',$status);
      return $this->db->execute();
    }
    public function getBannersbyRegionDependence($dependence,$status =1)
    {
      $this->getbanners();
      $this->db->where_equal('regionspecific',$dependence);
      $this->and_where('status',$status);
      return $this->db->execute();
    } 
    public function getBannerByRegion($regionId,$status = 1)
    {
      $this->getbanners();
      $this->db->join('inner',[['table'=>'bannerregion','field'=>'banner_id'],['table'=>'banners','field'=>'id']]);
      $this->db->where_equal('bannerregion.region_id',$regionId);
      $this->and_where('status',$status);
      return $this->db->execute();
    }
    public function getBannerByCountry($countryId,$status = 1)
    {
      $this->getbanners();
      $this->db->join('inner',[['table'=>'bannerregion','field'=>'banner_id'],['table'=>'banners','field'=>'id']]);
      $this->db->join('inner',[['table'=>'cities','field'=>'id'],['table'=>'bannerregion','field'=>'region_id']]);
      $this->db->where_equal('cities.country_id',$countryId);
      $this->and_where('bannerregion.status',$status);
      return $this->db->execute();
    }
    public function getBannerByAudienceAllRegionCountry($audience,$countryId,$status = 1)
    {
      $this->getbanners();
      $this->db->join('inner',[['table'=>'bannerregion','field'=>'banner_id'],['table'=>'banners','field'=>'id']]);
      $this->db->where_equal('bannerregion.country_id',$countryId);
      $this->db->and_where('bannerregion.region_id',0);
      $this->db->and_where('banners.audience',$audience);
      $this->and_where('bannerregion.status',$status);
      $this->and_where('banners.status',$status);
      return $this->db->execute();
    }
    public function getBannerByAudienceRegion($audience,$regionId,$status = 1)
    {
      $this->getbanners();
      $this->db->join('inner',[['table'=>'bannerregion','field'=>'banner_id'],['table'=>'banners','field'=>'id']]);
      $this->db->where_equal('bannerregion.region_id',$regionId);
      $this->db->and_where('banners.audience',$audience);
      $this->and_where('bannerregion.status',$status);
      $this->and_where('banners.status',$status);
      return $this->db->execute();
    }
    
    public function getBannerAllRegionCountry()
    {
      $this->getbanners();
      $this->db->join('inner',[['table'=>'bannerregion','field'=>'banner_id'],['table'=>'banners','field'=>'id']]);
      $this->db->where_equal('bannerregion.country_id',$countryId);
      $this->db->and_where('bannerregion.region_id',0);
      $this->and_where('bannerregion.status',$status);
      $this->and_where('banners.status',$status);
      return $this->db->execute();
    }
    public function getBannerByAudience($audience,$status =1)
    {
      $this->getbanners();
      $this->db->join('inner',[['table'=>'bannerregion','field'=>'banner_id'],['table'=>'banners','field'=>'id']]);
      $this->db->where_equal('banners.audience',$audience);
      $this->and_where('bannerregion.status',$status);
      $this->and_where('banners.status',$status);
      return $this->db->execute();
    }
    public function getBannerByMediaType($mediatype,$status = 1)
    {
      $this->getbanners();
      $this->db->join('inner',[['table'=>'bannerregion','field'=>'banner_id'],['table'=>'banners','field'=>'id']]);
      $this->db->where_equal('banners.mediatype',$mediatype);
      $this->and_where('bannerregion.status',$status);
      $this->and_where('banners.status',$status);
      return $this->db->execute();
    }
    public function getBannerByOwner($owner,$status = 1)
    {
      $this->getbanners();
      $this->db->join('inner',[['table'=>'bannerregion','field'=>'banner_id'],['table'=>'banners','field'=>'id']]);
      $this->db->where_equal('banners.mediatype',$mediatype);
      $this->and_where('bannerregion.status',$status);
      $this->and_where('banners.status',$status);
      return $this->db->execute();
    }
    public function getBannerByOwnerId($owner,$usertypeid,$status = 1)
    {
      $this->getbanners();
      $this->db->where_equal('banners.owner',$owner);
      $this->and_where('banners.usertype_id',$usertypeid);
      $this->and_where('banners.status',$status);
      return $this->db->execute();
    }
    public function getBannerByFilter($filter = array(),$sort = array())
    {
      $this->getbanners();
      $this->db->where_filter($filter);
      $this->sortBanners($sort);
      return $this->db->execute();
    }
    private function sortBanners($sort)
    {
      $limit  = isset($sort['limit']) ? $sort['limit'] : 30;
      $pageno = isset($sort['pageno']) ? $sort['pageno'] : 1;
      $offset = ($pageno - 1) * $limit;
      $this->db->limit($limit);
      $this->db->offset($offset);
      isset($sort['order_by']) && isset($sort['order_desc']) ? $this->db->order_by_desc(($sort['order_tbl'] ? $sort['order_tbl'] : ''),$sort['order_by']) : null;
      isset($sort['order_by']) && isset($sort['order_asc']) ? $this->db->order_by_asc(($sort['order_tbl'] ? $sort['order_tbl'] : ''),$sort['order_by']) : null;
      isset($sort['order_rand']) ? $this->db->order_rand() : null;
    }
  }
?>