<?php 
  class GCBanners Extends GController{

    public function addBanner($mediaurl,$mediatype,$owner,$audience,$inktype,$linkurl,$regionSpecific,$bannerRegions,$expdate,$promorequestId = 0,$ownerId = 0)
    {
      $this->load->model('banners');
      $this->model_banners->addBanner($mediatype,$mediaurl,$audience,$linktype,$linkurl,$regionspecific,$ownerId,$owner,$expdate,$promorequestId);
    }
    private function validateBanner()
    {
      $this->load->library("validator");
    }

    public function updateBanner()
    {

    }

    public function removeBanner($bannerId)
    {
      $this->load->model('banners');
      $bannerExist = $this->model_banners->getBannerById($bannerId);
      if(!$bannerExist['status']) return ['error'=>true,'message'=>"Could not find this banner"];
      $this->model_banners->changeBannerStatus($bannerId,0);
      return ['status'=>true,'message'=>"Banner has been successfully removed"];
    }

    public function updateBannerRegions($bannerId,$bannerRegions)
    {
      $this->load->model('banners');
  
      $bannerExist = $this->model_banners->getBannerById($bannerId);
      if(!$bannerExist['status']) return ['error'=>true,'message'=>"Could not find this banner"];
      $regions = array();
      $countries = array();
      foreach($bannerRegions as $region){ 
        if($region['regionid'] == 0)array_push($countries,$region['countryid']);
        else array_push($regions,$region['regionid']); 
      }
       /**CLOSE REMOVED REGIONS */
      $activeRegions =  $this->model_banners->getBannerRegions($bannerId,1);
      $activeRegions =  ($activeRegions['status']) ? $activeRegions['data'] : [];
      foreach($activeRegions as $active){
        if($active['region_id'] == 0){
          $activeRegionCountry  = $active['country_id'];
          if(!in_array($active['country_id'],$countries)){ 
            $this->model_banners->updateBannerRegionStatus($active['id'],0);
            array_splice($regions,array_search($countries,$active['country_id']),1); 
          }
        }else{
          if(!in_array($active['region_id'],$regions)){
            $this->model_banners->updateBannerRegionStatus($active['id'],0);
            array_splice($regions,array_search($regions,$active['region_id'],1));
          }
        }
      }
       /**REOPEN CLOSED REGIONS */
      $closedRegions =  $this->model_banners->getBannerRegions($bannerId,0);
      if($closedRegions['status'])$closedRegions =  $closedRegions['data'];
      foreach($closedRegions as $closed){
        if($close['region_id'] == 0){
          if(in_array($closed['country_id'],$countries)) $this->model_banners->updateBannerRegionStatus($closed['id'],1);
          array_splice($regions,array_search($regions,$closed['region_id'],1));
        }else{
          if(in_array($closed['region_id'],$regions)) $this->model_banners->updateBannerRegionStatus($closed['id'],1);
          array_splice($regions,array_search($regions,$closed['region_id'],1));
        }
      }
      foreach($regions as $region){ /**INSERT NEW REGION */
        $regionIndex = $this->getArrayIndexKey($bannerRegions,'region_id',$region);
        $countryId = $bannerRegions[$regionIndex]['countryid'];
        $this->model_banners->addBannerRegion($bannerId,$countryId,$region);
      }
      foreach($countries as $country){  $this->model_banners->addBannerRegion($bannerId,$country,0); }
      return ["status"=>true,"message"=>"Banner regions updated successfully!"];
    }

    private function getArrayIndexKey($array,$field,$value){
      foreach($array as $key=> $item){
        if($item[$field] == $value) return $key;
      } return -1;
    }

	  public function getBannersMarket($userRegionId,$user){
      $this->load->model('banners');
      $filters = [["field"=>"owner","value"=>1],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>1],["field"=>"regionspecific","value"=>0]];
      $sort = [ "order_rand"=>true,"limit"=>2];
      $mOrbBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      if($mOrbBanners['status'])$mOrbBanners = $mOrbBanners['data'];

      $filters = [["field"=>"owner","value"=>1,"sign"=> "<>"],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>1],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>0],["table"=>"bannerregion","field"=>"country_id","value"=>$user['countryid']]];
      $sort = [ "order_rand"=>true,"limit"=>2];
      $countryBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      if($countryBanners['status'])$countryBanners = $countryBanners['data'];
      else{
        $filters = [["field"=>"owner","value"=>1,"sign"=> "="],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>1],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>0],["table"=>"bannerregion","field"=>"country_id","value"=>$user['countryid']]];
        $sort = [ "order_rand"=>true,"limit"=>2];
        $countryBanners = $this->model_banners->getBannerByFilter($filters,$sort);
        if($countryBanners['status'])$countryBanners = $countryBanners['data'];
      }

      $filters = [["field"=>"owner","value"=>1,"sign"=> "<>"],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>1],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>$user['regionid']]];
      $sort = [ "order_rand"=>true,"limit"=>1];
      $regionBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      if($regionBanners['status'])$regionBanners = $regionBanners['data'];
      else{
        $filters = [["field"=>"owner","value"=>1],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>1],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>$user['regionid']]];
        $sort = [ "order_rand"=>true,"limit"=>2];
        $regionBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      }

      $banners = array_merge($countryBanners,$regionBanners,$mOrbBanners);
      return [ 'status'=>true,"data"=>array_unique($banners), "message"=>"Banners found!"];
    }
    public function getBannersSeller($userRegionId){
      $this->load->model('banners');
      $filters = [["field"=>"owner","value"=>1],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>2],["field"=>"regionspecific","value"=>0]];
      $sort = [ "order_rand"=>true,"limit"=>2];
      $mOrbBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      if($mOrbBanners['status'])$mOrbBanners = $mOrbBanners['data'];

      $filters = [["field"=>"owner","value"=>1,"sign"=> "<>"],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>2],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>0],["table"=>"bannerregion","field"=>"country_id","value"=>$user['countryid']]];
      $sort = [ "order_rand"=>true,"limit"=>2];
      $countryBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      if($countryBanners['status'])$countryBanners = $countryBanners['data'];
      else{
        $filters = [["field"=>"owner","value"=>1,"sign"=> "="],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>2],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>0],["table"=>"bannerregion","field"=>"country_id","value"=>$user['countryid']]];
        $sort = [ "order_rand"=>true,"limit"=>2];
        $countryBanners = $this->model_banners->getBannerByFilter($filters,$sort);
        if($countryBanners['status'])$countryBanners = $countryBanners['data'];
      }

      $filters = [["field"=>"owner","value"=>1,"sign"=> "<>"],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>2],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>$user['regionid']]];
      $sort = [ "order_rand"=>true,"limit"=>1];
      $regionBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      if($regionBanners['status'])$regionBanners = $regionBanners['data'];
      else{
        $filters = [["field"=>"owner","value"=>1],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>2],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>$user['regionid']]];
        $sort = [ "order_rand"=>true,"limit"=>2];
        $regionBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      }

      $banners = array_merge($countryBanners,$regionBanners,$mOrbBanners);
      return [ 'status'=>true,"data"=>array_unique($banners), "message"=>"Banners found!"];
    }
    public function getBannersCourier($userRegionId){
      $this->load->model('banners');
      $filters = [["field"=>"owner","value"=>1],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>3],["field"=>"regionspecific","value"=>0]];
      $sort = [ "order_rand"=>true,"limit"=>2];
      $mOrbBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      if($mOrbBanners['status'])$mOrbBanners = $mOrbBanners['data'];

      $filters = [["field"=>"owner","value"=>1,"sign"=> "<>"],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>3],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>0],["table"=>"bannerregion","field"=>"country_id","value"=>$user['countryid']]];
      $sort = [ "order_rand"=>true,"limit"=>2];
      $countryBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      if($countryBanners['status'])$countryBanners = $countryBanners['data'];
      else{
        $filters = [["field"=>"owner","value"=>1,"sign"=> "="],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>3],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>0],["table"=>"bannerregion","field"=>"country_id","value"=>$user['countryid']]];
        $sort = [ "order_rand"=>true,"limit"=>2];
        $countryBanners = $this->model_banners->getBannerByFilter($filters,$sort);
        if($countryBanners['status'])$countryBanners = $countryBanners['data'];
      }

      $filters = [["field"=>"owner","value"=>1,"sign"=> "<>"],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>3],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>$user['regionid']]];
      $sort = [ "order_rand"=>true,"limit"=>1];
      $regionBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      if($regionBanners['status'])$regionBanners = $regionBanners['data'];
      else{
        $filters = [["field"=>"owner","value"=>1],["field"=>"expdate","value"=>"NOW()", "sign"=> "<="],["field"=>"status","value"=>1,"table"=>"banners"],["field"=>"audience","value"=>3],["field"=>"regionspecific","value"=>1],["field"=>"regionspecific","value"=>1],["table"=>"bannerregion","field"=>"region_id","value"=>$user['regionid']]];
        $sort = [ "order_rand"=>true,"limit"=>2];
        $regionBanners = $this->model_banners->getBannerByFilter($filters,$sort);
      }

      $banners = array_merge($countryBanners,$regionBanners,$mOrbBanners);
      return [ 'status'=>true,"data"=>array_unique($banners), "message"=>"Banners found!"];
    }

    public function getSellerActiveBanners($sellerId)
    {
      $this->load->model('banners');
      $sellerBanners = $this->model_banners->getBannerByOwnerId(4,$sellerId);
      if($sellerBanners['status']) [ "status"=>true,"data"=>$sellerBanners['data']];
      return ["error"=>true,"message"=>"No active banners for this account"];
    }
    public function getCourierActiveBanners($courierId)
    {
      $this->load->model('banners');
      $courierBanners = $this->model_banners->getBannerByOwnerId(5,$courierId);
      if($courierBanners['status']) [ "status"=>true,"data"=>$courierBanners['data']];
      return ["error"=>true,"message"=>"No active banners for this account"];
    }
    public function getMarketOrbActiveBanners()
    {
      $this->load->model('banners');
      $mOrbBanners = $this->model_banners->getBannerByOwner(1);
      if($mOrbBanners['status']) [ "status"=>true,"data"=>$mOrbBanners['data']];
      return ["error"=>true,"message"=>"No administrator active banners currently"];
    }

    public function getExternalActiveBanners($tradeId)
    {
      $this->load->model('banners');
      $this->load->model('users');
      $userExists = $this->model_users->getUserbyTradeId($tradeId);
			if(!$userExists['status']) return  ['error'=>true,'message'=>'Invalid user!'];
      $banners = $this->model_banners->getBannerByOwnerId(3,$userExists['data']['userid']);
      if($banners['status']) [ "status"=>true,"data"=>$banners['data']];
      return ["error"=>true,"message"=>"No banners for this account!"];
    }

    public function getSellerExpiredBanners($tradeId)
    {
      $this->load->model('banners');
      $this->load->model('seller');
      $userExists = $this->model_seller->getSellerbyTradeId($tradeId);
			if(!$userExists['status']) return  ['error'=>true,'message'=>'No seller account associated with this user!'];
      $banners = $this->model_banners->getBannerByOwnerId(4,$userExists['data']['msellerid'],3);
      if($banners['status']) [ "status"=>true,"data"=>$banners['data']];
      return ["error"=>true,"message"=>"No expired banners for this account!"];
    }
    public function getCourierExpiredBanners($tradeId)
    {
      $this->load->model('banners');
      $this->load->model('courier');
      $userExists = $this->model_courier->getCourierbyTradeId($tradeId);
      if(!$userExists['status']) return  ['error'=>true,'message'=>'No courier account associated with this user!'];
      $banners = $this->model_banners->getBannerByOwnerId(5,$userExists['data']['mcourierid'],3);
      if($banners['status']) [ "status"=>true,"data"=>$banners['data']];
      return ["error"=>true,"message"=>"No expired banners for this account!"];
    }
    public function getMarketOrbExpiredBanners()
    {
      $this->load->model('banners');
      $mOrbBanners = $this->model_banners->getBannerByOwner(1,3);
      if($mOrbBanners['status']) [ "status"=>true,"data"=>$mOrbBanners['data']];
      return ["error"=>true,"message"=>"No administrator active banners currently"];
    }
    public function getExternalExpiredBanners($tradeId)
    {
      $this->load->model('banners');
      $userExists = $this->model_users->getUserbyTradeId($tradeId);
			if(!$userExists['status']) return  ['error'=>true,'message'=>'Invalid user!'];
      $this->model_banners->getBannerByOwner(3);
    }

    public function getSellerBanners($tradeId)
    {
      $this->load->model('banners');
      $this->load->model('seller');
      $userExists = $this->model_seller->getSellerbyTradeId($tradeId);
      if(!$userExists['status']) return  ['error'=>true,'message'=>'No seller account associated with this user!'];
      $banners = array();
      $suspendedbanners = $this->model_banners->getBannerByOwnerId(4,$userExists['data']['msellerid'],2);
      if($suspendedbanners['status']) array_merge($banners,$suspendedbanners['data']);
      $expiredbanners = $this->model_banners->getBannerByOwnerId(4,$userExists['data']['msellerid'],3);
      if($expiredbanners['status']) array_merge($banners,$expiredbanners['data']);
      $activebanners = $this->model_banners->getBannerByOwnerId(4,$userExists['data']['msellerid'],1);
      if($activebanners['status']) array_merge($banners,$activebanners['data']);

      return ["error"=>true,"message"=>"No expired banners for this account!"];
    }

    public function getCourierBanners()
    {
      $this->load->model('banners');
      $this->load->model('seller');
      $userExists = $this->model_seller->getSellerbyTradeId($tradeId);
			if(!$userExists['status']) return  ['error'=>true,'message'=>'No seller account associated with this user!'];
      $banners = $this->model_banners->getBannerByOwnerId(4,$userExists['data']['msellerid'],3);
      if($banners['status']) [ "status"=>true,"data"=>$banners['data']];
      return ["error"=>true,"message"=>"No expired banners for this account!"];
    }

    public function getEternalBanners()
    {
      $this->load->model('banners'); 
      $this->load->model('users');
      $userExists = $this->model_users->getUserbyTradeId($tradeId);
      if(!$userExists['status']) return  ['error'=>true,'message'=>'No account associated with this user!'];
      $banners = $this->model_banners->getBannerByOwnerId(4,$userExists['data']['msellerid'],2);
      if($banners['status']) [ "status"=>true,"data"=>$banners['data']];
      return ["error"=>true,"message"=>"No expired banners for this account!"];
    }

    public function getActiveExpiredBanners()
    {
      $filters = [["field"=>"expdate","value"=>"NOW()", "sign"=> ">"],["field"=>"status","value"=>1,"table"=>"banners"]];
      $banners = $this->model_banners->getBannerByFilter($filters,[]);
      if(!$banners['status']) return [ "error"=>true,"message"=>"No active expired banner"];
      return [ 'status'=>true,"data"=>array_unique($banners), "message"=>"Banners found!"];
    }

    public function getRegionBanners()
    {

    }

    public function getStateBanners()
    {

    }

    public function getCountryBanners()
    {

    }

    public function getStateExpiredBanners()
    {

    }

    public function getBannersByFilters()
    {

    }
  }
?>
