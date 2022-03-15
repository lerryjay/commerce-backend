<?php 
  class GMPromos Extends GModel{
    public function addPromoRequest($promotype,$reference,$usertype,$usertypeid,$duration,$startdate,$linktype,$linkId,$approval,$promonote,$audience,$regionspecific)
    {
      return $this->db->insert('promorequests',
        array(
          'type'=>$promotype,
          'reference'=>$reference,
          'audience'=>$audience,
          'usertype_id'=>$usertypeid,
          'usertype'=>$usertype,
          'duration'=>$duration,
          'startdate'=>$startdate,
          'regionspecific'=>$regionspecific,
          'note'=>$note,
          'linktype'=>$linktype,
          'linktypeid'=>$linkid
        )
      );
    }
    public function updatePromoRequest($promorequestId,$promotype,$reference,$usertype,$usertypeid,$duration,$startdate,$linktype,$linkId,$approval,$promonote,$audience,$regionspecific)
    {
      return $this->db->updateTableRecord('promorequests',
        array(
          'type'=>$promotype,
          'reference'=>$reference,
          'audience'=>$sku,
          'usertype_id'=>$price,
          'usertype'=>$promo_price,
          'duration'=>$duration,
          'startdate'=>$startdate,
          'regionspecific'=>$regionspecific,
          'note'=>$note,
          'linktype'=>$linktype,
          'linktypeid'=>$linkid
        ),
        [
          'promorequest_id'=>$promorequestId
        ]
      ); 
    }
    public function addPromoRequestRegion($promorequestId,$regionId)
    {
      return $this->db->insert('promorequestregion',
        array(
          'region_id'=>$regionId,
          'promorequest_id'=>$promorequestId
        )
      ); 
    }
    public function updatePromoRequestRegionStatus($promoRequestRegionId,$status = 1)
    {
      return $this->db->updateTableRecord('promorequestregion',
        array(
          'status'=>$status,
        ),
        [
          'id'=>$promoRequestRegionId
        ]
      ); 
    }
   
    public function addPromoRequestMedia($promorequestId,$mediatype,$mediaurl)
    {
      return $this->db->insert('promorequestmedia',
        array(
          'mediatype'=>$mediatype,
          'mediaurl'=>$mediaurl,
          'promorequest_id'=>$promorequestId
        )
      ); 
    }
    public function updatePromoRequestMediaStatus($promoRequestMediaId,$status = 1)
    {
      return $this->db->updateTableRecord('promorequestmedia',
        array(
          'status'=>$status,
        ),
        [
          'id'=>$promoRequestMediaId
        ]
      );
    }
    public function updatePromoRequestApproval($promorequestId,$status = 1)
    {
      return $this->db->updateTableRecord('promorequests',
        array(
          'approval'=>$approval,
        ),
        [
          'promorequest_id'=>$promorequestId
        ]
      ); 
    }
    public function updatePromoRequestStatus($promorequestId,$status = 1)
    {
      return $this->db->updateTableRecord('promorequests',
        array(
          'status'=>$status,
        ),
        [
          'promorequest_id'=>$promorequestId
        ]
      ); 
    }
    
    public function registerAds($name,$promorequestId,$dateadded,$expdate)
    {
      return $this->db->insert('ads',
        array(
          'name'=>$name,
          'promorequest_id'=>$promorequestId,
          'dateadded'=>$dateadded,
          'expdate'=>$expdate,
        )
      ); 
    }
    public function addAdsRegion($adsId,$regionId)
    {
      return $this->db->insert('adsregion',
        array(
          'region_id'=>$regionId,
          'ads_id'=>$adsId
        )
      ); 
    }
    public function addAdsMedia($adsId,$mediatype,$mediaurl)
    {
      return $this->db->insert('adsmedia',
        array(
          'mediatype'=>$mediatype,
          'mediaurl'=>$mediaurl,
          'ads_id'=>$adsId
        )
      ); 
    }

   
    private function getpromorequests($fields = array())
    {
      $this->db->query('promorequests',
      array_merge($fields,[
        'type','reference','audience','usertype_id','usertype','duration','startdate','regionspecific','note','linktype','linktypeid'
      ]));
    }

    public function getPromoRequestByApproval($approval,$status = 1)
    {
      $this->getpromorequests();
      $this->db->where_equal('promorequests.approval',$approval);
      $this->db->and_where('promorequests.status',$status);
      return $this->db->execute();
    }
    public function getPromoRequestByUserType($usertype,$status = 1)
    {
      $this->getpromorequests();
      $this->db->where_equal('promorequests.usertype',$usertypeid);
      $this->db->and_where('promorequests.status',$status);
      return $this->db->execute();
    }
    public function getPromoRequestByUser($usertypeid,$usertype,$status = 1)
    {
      $this->getpromorequests();
      $this->db->where_equal('promorequests.usertype_id',$usertypeid);
      $this->db->and_where('promorequests.usertype',$usertype);
      $this->db->and_where('promorequests.status',$status);
      return $this->db->execute();
    }
    public function getPromoRequestsByRegion($regionId,$status = 1)
    {
      $this->getpromorequests();
      $this->db->join('inner',
        [
          [
            "table"=>"promorequestregion",
            "field"=>"promorequest_id"
          ],
          [
            "table"=>"promorequests",
            "field"=>"id"
          ]
        ]
      );
      $this->db->where_equal('promorequestregion.region_id',$regionid);
      $this->db->and_where('promorequestregion.status',$status);
      return $this->db->execute();
    }
    public function getPromoRequestsByMediaType($mediatype,$status = 1)
    {
      $this->getpromorequests();
      $this->db->join('inner',
        [
          [
            "table"=>"promorequestmedia",
            "field"=>"promorequest_id"
          ],
          [
            "table"=>"promorequests",
            "field"=>"id"
          ]
        ]
      );
      $this->db->where_equal('promorequestmedia.mediatype',$mediatype);
      $this->db->and_where('promorequestmedia.status',$status);
      return $this->db->execute();
    }
    public function getPromoRequestByCountry($countryId,$status = 1)
    {
      $this->getpromorequests();
      $this->db->join('inner',
        [
          [
            "table"=>"promorequestregion",
            "field"=>"promorequest_id"
          ],
          [
            "table"=>"promorequests",
            "field"=>"id"
          ]
        ]
      );
      $this->db->join('inner',
        [
          [
            "table"=>"cities",
            "field"=>"id"
          ],
          [
            "table"=>"promorequestregion",
            "field"=>"region_id"
          ]
        ]
      );
      $this->db->where_equal('cities.country_id',$countryId);
      $this->db->and_where('promorequestregion.status',$status);
      return $this->db->execute();
    }

    public function getPromoRequestMedia($promorequestId,$status = 1)
    {
      $this->db->query('promorequestmedia',['mediatype','mediaurl']);
      $this->db->where_equal('promorequestmedia.promorequest_id',$promorequestId);
      $this->db->and_where('promorequestmedia.status',$status);
      return $this->db->execute();
    }
    public function getPromoRequestRegion($promorequestId,$status = 1)
    {
      $this->db->query('promorequestregion',[ 'region_id','promorequest_id']) ;
      $this->db->where_equal('promorequestregion.promorequest_id',$promorequestId);
      $this->db->and_where('promorequestregion.status',$status);
      return $this->db->execute();
    }
    
    public function getPromoRequestByFilter($filter)
    {

    }

    
  }
?>