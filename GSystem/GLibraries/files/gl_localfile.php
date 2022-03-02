<?php 
  class GLFilesLocalFile extends GLibrary
  {
    private $imagefiletypes;

    public function uploadSingleImage($storagepath,$newname)
    {
			$target_dir = "images/".$storagepath.'/';
      $extensionsArr = array("jpg","jpeg","png","gif");
      return $this->upload($storagePath,$newName,$extensionsArr,300,$filekey);
    }
    
    public function uploadSingleDoc($storagepath,$newName,$filekey = 'file')
    {
      
			$target_dir = "images/".$storagepath.'/';
      $extensionsArr = array("jpg","jpeg","png","gif","pdf","doc","docx","xmls",'zip');
      return $this->upload($storagePath,$newName,$extensionsArr,300,$filekey);
    }

    private  function uploadsingle($storagePath,$newName = null,$allowExtensions= [],$maxSize=200,$filekey = 'file')
    {
      $file = $_FILES[$filekey];
      $name = $file['name'];
      $target_file = $storagePath.basename($file['name']);
      // Select file type
      if (!file_exists($storagePath)) { mkdir($storagePath, 0777, true);}
      $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
      if(!in_array($fileType,$allowExtensions)) return ['status'=>false,'message'=>'Unsuupported file type for upload. Only'.implode(",",$allowExtensions).' allowed' ];
      if($file['size'] > ($maxSize * 1024)) return ['status'=>false,'message'=>'File too large! Maximum file upload size is '.$maxSize.' KB'];
      $newName = isset($newName) && strlen($newName) > 1 ? $newName.".".$fileType : $name;
      if(move_uploaded_file($file['tmp_name'],$storagePath.'/'.$newName)) return ["status"=>true,'message'=>'Upload successful!','data'=>['success'=>["path"=>$storagePath.'/'.$newName]]];
			else ["status"=>false,"message"=>"An error was encountered!"];
    }

    public function uploadMultipleImages($storagePath,$newname)
    {
			$target_dir = "public/images/".$storagePath.'/';
      $extensionsArr = array("jpg","jpeg","png","gif");
      return $this->upload($$storagePath,$extensionsArr,300);
    }
    
    public function uploadMultipleDocs($storagePath,$filekey='files')
    {
			$target_dir = "public/".$storagePath.'/';
      $extensionsArr = array("jpg","jpeg","png","gif","pdf","doc","docx","xmls",'zip');
      return $this->uploadMultiple($target_dir,$extensionsArr,300,$filekey);
    }

    private  function uploadMultiple($storagePath,$allowExtensions= [],$maxSize=200,$filekey= 'file')
    {
      if(!isset($_FILES[$filekey])) return ['status'=>false,'message'=>'File not found or invalid key passed'];
      $file = $_FILES[$filekey];
      if(!is_array($file['name'])) return $this->uploadsingle($storagePath,null,$allowExtensions,$maxSize,$filekey);
      $count = count($file['name']);
      $success = [];
      $errors = [];
      if (!file_exists($storagePath)) { mkdir($storagePath, 0777, true);}
      for ($i=0; $i < $count; $i++) { 
        $name = $file['name'][$i];
        $target_file = $storagePath.basename($file['name'][$i]);
        // Select file type
        $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        if(!in_array($fileType,$allowExtensions)) array_push($errors,['file'=>$file['name'][$i],'message'=>'Unsuupported file type for upload. Only'.implode(",",$allowExtensions).' allowed']);
        elseif($file['size'][$i] > ($maxSize * 1024))  array_push($errors,['file'=>$file['name'][$i],'message'=>'File too large! Maximum file upload size is '.$maxSize.' KB']);
        elseif(move_uploaded_file($file['tmp_name'][$i],$storagePath.'/'.$file['name'][$i])) array_push($success,['name'=>$file['name'][$i], 'path'=>$storagePath.'/'.$file['name'][$i]]);
        else array_push($errors,['file'=>$file['name'][$i],'message'=>'Error uploading file']);
      }
      if(count($success) > 0) return ['status'=>true,'message'=>'Files uploaded','data'=>['uploaded'=>count($success), 'failed'=>count($errors),'errors'=>$errors, 'success'=>$success ]];
      else ['status'=>false, 'message'=>'File upload failed'];
    }

    public function getFileMimeType($filepath)
    {
      $fileType = strtolower(pathinfo($filepath,PATHINFO_EXTENSION));
      if(in_array($fileType,IMAGE_FILE_FORMAT)){ 
        if($fileType == 'ico' || $fileType == 'cur') return 'image/x-icon';
        elseif($fileType == 'svg') return 'image/svg+xml';
        elseif($fileType == 'tif'  || $fileType == 'tiff') return 'image/tif';
        elseif(in_array($fileType,['jpg','jpeg','jfif','pjpeg','pjp'])) return 'image/x-icon';
        else return 'image/'.$fileType;
      }elseif(in_array($fileType,AUDIO_FILE_FORMAT)){
        // if($fileType == 'mp3') 
        return 'audio/mp3';
      }elseif(in_array($fileType,VIDEO_FILE_FORMAT)){
        if(in_array($fileType,['mpe','mpeg','mpa','mpg'])) return 'video/mpeg';
        elseif($fileType == 'avi') return 'video/x-msvideo';
        return 'audio/mp4';
      }elseif(in_array($fileType,DOC_FILE_FORMAT)){
        if(in_array($fileType,['html','htm','stm'])) return 'text/html';
        elseif(in_array($fileType,['xlm','xlt','xlc','xla','xls','xlw'])) return '	application/vnd.ms-excel';
        elseif(in_array($fileType,['pot','pps','ppt'])) return 'application/vnd.ms-excel';
        elseif(in_array($fileType,['doc','docx'])) return 'application/msword';
        elseif($fileType == 'pdf') return 'application/pdf';
        else return 'text/plain';
      }else if(in_array($fileType,ZIP_FILE_FORMAT)) {
        if($fileType == 'zip') return 'application/zip';
      }else return 'application/force-download';
    }
  }
?>