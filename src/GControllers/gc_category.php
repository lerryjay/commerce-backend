<?php 
  class GCCategory Extends GController{

    public function addCategory($name,$description,$setmain = false,$parent = array(),$sub = array())
    {
      $valid = $this->validateCategory($name,$description);
      if(!$valid['status']) return $valid;
      $this->load->model('category');
      $insert = $this->model_category->addCategory($name,$description);
      if(!$insert) return [ "error"=>true,"message"=>"An error was ecountered"];
      if($setmain) $this->model_category->addParentRelation(0,$insert);
      if(count($sub) < 1 &&  count($root) < 1) return [ "status"=>true,"message"=>"Category added successfully"];
      foreach($parent as $root){
        $checkRoot = $this->model_category->getCategoryById($root);
        if($checkRoot['status'])$this->model_category->addParentRelation($root,$insert);
      }
      foreach($sub as $child){
        $checkSub = $this->model_category->getCategoryById($child);
        if($checkSub['status'])$this->model_category->addParentRelation($insert,$child);
      }
      return [ "status"=>true,"message"=>"Category added successfully!"];
    }
    public function updateCategory($categoryId,$name,$description)
    {
      $this->load->model('category');
      $checkCategory = $this->model_category->getCategoryById($categoryId);
      if(!$checkCategory['status']) return [ "error"=>true,"message"=>"Category not found!"];
      $valid = $this->validateCategory($name,$description);
      if(!$valid['status']) return $valid;
      $update = $this->model_category->updateCategory($categoryId,$name,$description);
      if(!$update) return [ "error"=>true,"message"=>"An error was ecountered"];
      return [ "status"=>true,"message"=>"Category updated successfully!"];
    }
    public function addCategoryRoot($parent,$sub)
    {
      $checkRoot = $this->model_category->getCategoryRelation($parent,$sub);
      if($checkRoot['status']){
        if($checkRoot['data']['root_id'] == $parent) return ["error"=>true,"message"=>"This relationship as alreadu been established!"];
        else return ["error"=>true,"message"=>"A sub category can't be parent to one of its root!"];
      }
      $insert  = $this->model_category->addParentRelation($parent,$insert);
      if(!$insert) return [ "error"=>true,"message"=>"An error was ecountered"];
      return ["status"=>true,"message"=>"Settings saved!"];
    }
    public function updateCategorySub($categoryId,$subs = array())
    {
      foreach($subs as $sub){
        $checkSub = $this->model_category->getCategoryById($sub);
        if($checkSub['status']){
          $checkRelation = $this->model_category->getCategoryRelation($parent,$sub);
          if($checkRelation['status']){
            if($checkRelation['data']['root_id'] == $categoryId) return ["error"=>true,"message"=>"This relationship as alreadu been established!"];
            else return ["error"=>true,"message"=>"A sub category can't be parent to one of its root!"];
          }
          if($checkSub['status'])$this->model_category->addParentRelation($categoryId,$sub);
        }
        
      }
    }
    public function getRootCategories()
    {
      $this->load->model('category');
      $getRoot = $this->model_category->getMainCategories();
      if($getRoot['status']) return $getRoot;
      return ["error"=>true,"message"=>"Category has no sub!"];
    }
    public function getSubCategories($categoryId)
    {
      $this->load->model('category');
      $checkCategory = $this->model_category->getCategoryById($categoryId);
      if(!$checkCategory['status']) return ["error"=>true,"message"=>"Category not found!"];
      $getSub = $this->model_category->getSubCategory($categoryId);
      if($getSub['status']) return $getSub;
      return ["error"=>true,"message"=>"Category has no sub!"];
    }
    public function getCategoryRoot($categoryId)
    {
      $this->load->model('category');
      $checkCategory = $this->model_category->getCategoryById($categoryId);
      if(!$checkCategory['status']) return ["error"=>true,"message"=>"Category not found!"];
      $getRoot = $this->model_category->getParentCategory($categoryId);
      if($getRoot['status']) return $getRoot;
      $checkRoot = $this->model_category->getCategoryRelation(0,$categoryId);
      if($checkRoot['status']) return ["status"=>true,"data"=>[ "id"=>0,"name"=>"Root","message"=>"Category is a root category"]];
      return ["error"=>true,"message"=>"Couldn't establish category root!"];
    }
    public function updateCategoryImage()
    {

    }
    private function validateCategory($name,$description)
    {
      $this->load->library('validator');
      if(!$this->library_validator->cleanString($name,1,25)) return ["error"=>true,"message"=>"Invalid Category name. Only letters and spaces allowed!"];
      if(!$this->library_validator->cleanString($name,1,200)) return ["error"=>true,"message"=>"Invalid Category descriptiom. Only letters and spcaes allowed,max 200 characters"];
      return ["status"=>true];
    }
  }
?>