<?php 
  class GMSections Extends GModel{
    private $maintbl = 'sections';


    public function addSection($name,$description,$display,$maxproduct,$selectproduct = 0)
    {
      return $this->db->insert($this->maintbl,
        array(
          'description' => $description,
          'name'=>$name,
          'display'=>$display,
          'maxproduct'=>$maxproduct,
          'selectproduct'=>$selectproduct
        )
      );
    }

    public function addSectionAudience($sectionId,$audienceId,$value)
    {
      return $this->db->insert('sectionaudience',
        array(
          'audience_id' =>$audienceId,
          'section_id'=>$sectionId,
          'value'=>$value
        )
      );
    }

    public function addSectionRegion($sectionId,$countryId,$regionId)
    {
      return $this->db->insert('productcategory',
        array(
          'region_id' =>$regionId,
          'section_id'=>$sectionId,
          'country_id'=>$countryId
        )
      );
    }

    public function addSectionProduct($sectionId,$productId)
    {
      return $this->db->insert('sectionproduct',
        array(
          'product_id' =>$productId,
          'section_id'=>$sectionId
        )
      );
    }


    public function updateSection($sectionId,$name,$description,$display,$maxproduct,$selectproduct = 0)
    {
      return $this->db->update($this->maintbl,
        [
          'description' => $description,
          'name'=>$name,
          'display'=>$display,
          'maxproduct'=>$maxproduct,
          'selectproduct'=>$selectproduct
        ],
        [
          'id'=>$sectionId
        ]
      );
    }
    public function updateSectionStatus($sectionId,$status = 1)
    {
      return $this->db->update('sections',
        [
          'views'=>'views + '.$count,
        ],
        [
          'id'=>$sectionId
        ]
      );
    }

    public function updateSectionRegionStatus($sectionregionId,$status = 1)
    {
      return $this->db->update('sectionregion',
        [
          'views'=>'views + '.$count,
        ],
        [
          'id'=>$sectionregionId
        ]
      );
    }

    public function updateSectionProductStatus($sectionId,$productId,$status = 1)
    {
      return $this->db->update('sections',
        [
          'status'=>$status
        ],
        [
          'section_id'=>$sectionId,
          'product_id'=>$productId
        ]
      );
    }

    public function updateSectionViews($sectionId,$count = 1)
    {
      return $this->db->update($this->maintbl,
        [
          'views'=>'views + '.$count,
        ],
        [
          'id'=>$sectionId
        ]
      );
    }
    private function getallsections()
    {
      return $this->db->query($this->maintbl,[ 'description','name','display','maxproduct','select']);
    }

    public function getSections($status = 1)
    {
      return $this->getallsections()->where_equal('status',$status);
    }

    public function getRegionSections()
    {

    }

    public function getCountrySections()
    {

    }


    private function getallsectionproducts($fields = [])
    {
      return $this->db->query('sectionproducts',array_merge($fields,['section_id','product_id']));
    }

    public function getSectionProducts($sectionId,$status =1)
    {
      $this->getallsectionproducts();
      $this->db->where_equal('status',$status);
      $this->db->and_where('section_id',$sectionId);
      return $this->db->execute();
    }

    private function getallsectionaudience()
    {
      return $this->db->query('sectionaudience',array_merge($fields,['section_id','audience_id)']) );
    }

    public function getSectionAudience($sectionId,$status = 1)
    {
      return $this->getallsectionaudience()->where_equal('status',$status)->and_where('section_id',$sectionId)->execute();
    }

    public function getSectionAudienceProducts($sectionId,$sectionAudience = array(),$sastatus = 1,$pastatus = 1)
    {

      //SELECT DISTINCT pa.product_id FROM  `sectionaudience` INNER JOIN `productaudience` AS pa ON pa.audience_id = sectionaudience.audience_id INNER JOIN `productaudience` AS pav ON pav.value = sectionaudience.value WHERE EXISTS  AND EXISTS ( SELECT * FROM `productaudience` WHERE pa.product_id = product_id AND audience_id = 3 AND value = 2)
      $this->db->query('sectionaudience', ['DISTINCT pa.product_id']
      )->join('inner', [
        [
          "table"=>"productaudience",
          "field"=>"audience_id",
          "as"=>"pa"
        ],
        [
          "table"=>"sectionaudience",
          "field"=>"audience_id"
        ]
      ])->join('inner', [
        [
          "table"=>"productaudience",
          "field"=>"value",
          "as"=>"pav"
        ],
        [
          "table"=>"sectionaudience",
          "field"=>"value"
        ]
      ])->where_equal('sectionaudience.status',$sastatus)->and_where('productaudience.status',$pastatus)->and_where('section_id',$sectionId,'sectionaudience');
      foreach($sectionAudience as $item){
        $this->db->and_where_exists('( SELECT * FROM productaudience WHERE pa.product_id = product_id  AND audience_id = ?  AND value = ? )',[$item['audience_id'],$item['value']]); 
      }
      return $this->db->execute();
    } 

    
  }
?> 