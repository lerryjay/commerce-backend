<?php 
  class GLDbMysql Extends GLibrary
  {
  	private $sql = '';
  	private $where = '';
  	private $join = '';
  	private $limit =  '';
  	private $order = '';
  	private $offset = '';
  	private $string = '';
		private $parameters = array();
		
		public	$count;
		public	$rows;
		public	$row;

		public function connect(){
			global $db_server;
			global $db_user;
			global $db_password;
			global $db; 
			try {
				$this->dbconn =  new mysqli($db_server, $db_user, $db_password, $db);
				return $this->dbconn;
			} catch (Exception $e) {
				return $e; // should throw connection error
			}
			
		}

		public function query($tablename, $fields){
			$this->sql = " SELECT ".implode(',',  $fields)." FROM $tablename ";
			return $this;
		 }
		 
		public function addParam($values)
		{
			 foreach($values  as $value ){
				if (is_numeric($value)) {	$this->string .= 'i';}else{ $this->string .= 's';}
				array_push($this->parameters, $this->dbconn->real_escape_string($value));
			}
			return $this;
    }
    
    public function addField($fields = [])
    {
      $this->sql = 'SELECT '.implode(',',$fields).ltrim(implode(',',explode('SELECT', $this->sql,2)), ',');
      return $this;
    }

	 	public function join($type,$fields){
	 		$type = strtoupper($type);
	 		if (in_array($type,array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'))) {
	 			$this->join .= ' '.$type.' JOIN '.( $fields[0]["table"].(isset($fields[0]["as"]) ? ' AS '.$fields[0]["as"] : ''  )).' ON '.(isset($fields[0]["as"]) ? $fields[0]["as"] : $fields[0]["table"]).'.'.$fields[0]['field'].' = '.$fields[1]["table"].'.'.$fields[1]['field'].' ';
			 }
			 return $this;
	 	}

	 	public function where_equal($field,$value,$type = '',$table = ''){
	 		if (strlen($this->where) > 0) {
	 			if (strlen($table) > 1) {
	 				$this->where .= $type.' '.$table.'.'.$field.' = ? ';
	 			}else{
	 				$this->where .= $type.' '.$field.' = ? ';
	 			}
	 		}else{
	 			if (strlen($table) > 0) {
	 				$this->where = 'WHERE '.$table.'.'.$field.' = ? ';
	 			}else{
	 				$this->where = 'WHERE '.$field.' = ? ';
	 			}
	 		}
	 		$this->addParam([$value]);
			return $this;
		}

	 	public function and_where($field,$value,$table = ''){
			return $this->where_equal($field,$value,'AND',$table);
		 }
		 
	 	public function or_where($field,$value,$table = ''){
			return $this->where_equal($field,$value,'OR',$table);
		 }
		 
	 	public function where_equal_query($field,$query,$values,$type = '',$table = ''){
	 		if (strlen($this->where) > 0) {
	 			if (strlen($table) > 1) {
	 				$this->where .= $type.' '.$table.'.'.$field.' = ('.$query.' ) ';
	 			}else{
	 				$this->where .= $type.' '.$field.' = ('.$query.' ) ';
	 			}
	 		}else{
	 			if (strlen($table) > 0) {
	 				$this->where = 'WHERE '.$table.'.'.$field.' = ('.$query.' ) ';
	 			}else{
	 				$this->where = 'WHERE '.$field.' = ('.$query.' ) ';
	 			}
	 		}
	 		$this->addParam($values);
			return $this;
		}


	 	public function and_where_equal_query($field,$query,$value,$table = ''){
			return $this->where_equal_query($field,$query,$value,'AND',$table);
		 }
		 
		 

	 	public function or_where_equal_query($field,$query,$value,$table = ''){
			return $this->where_equal_query($field,$query,$value,'OR',$table);
	 	}

	 	private function where_like($field,$value,$type = '',$table = ''){
	 		if (strlen($this->where) > 0) {
	 			if (strlen($table) > 1) {
	 				$this->where .= $type.' '.$table.'.'.$field.' LIKE ? ';
	 			}else{
	 				$this->where .= $type.' '.$field.' LIKE ? ';
	 			}
	 		}else{
	 			if (strlen($table) > 0) {
	 				$this->where = 'WHERE '.$table.'.'.$field.' LIKE ? ';
	 			}else{
	 				$this->where = 'WHERE '.$field.' LIKE ? ';
	 			}
	 		}
			$this->addParam([$value]);
			return $this;
	 	}

	 	public function and_where_like($field,$value,$table = ''){
			return $this->where_like($field,$value,'AND',$table);
	 	}

	 	public function or_where_like($field,$value,$table = ''){
			return $this->where_like($field,$value,'OR',$table);
	 	}

		public function where_in($field,$value,$params = [],$type = '',$table = ''){
			if (strlen($this->where) > 0) {
			 if (strlen($table) > 1) {
				 $this->where .= $type.' '.$table.'.'.$field.' IN '.$value;
			 }else{
				 $this->where .= $type.' '.$field.' IN '.$value;
			 }
			}else{
			 if (strlen($table) > 0) {
				 $this->where = 'WHERE '.$table.'.'.$field.' IN '.$value;
			 }else{
				 $this->where = 'WHERE '.$field.' IN '.$value;
			 }
			}
			$this->addParam($params);
			return $this;
		}

		public function and_where_in($field,$value,$params = [],$table = ''){
			return $this->where_in($field,$value,$params,'AND',$table);
		}

		public function or_where_in($field,$value,$params = [],$table = ''){
			return $this->where_in($field,$value,$params,'OR',$table);
		}

		public function where_exists($query,$params = [],$type = ''){
			if (strlen($this->where) > 0)  $this->where .= $type.' EXISTS ('.$query.' ) ';
			else $this->where = 'WHERE EXISTS ('.$query.' ) ';
			$this->addParam($params);
			return $this;
		}

		public function and_where_exists($query,$params = []){
			return $this->where_exists($query,$params,'AND');
		}

		public function or_where_exists($query,$params = []){
			return $this->where_exists($query,$params,'OR');
		}


	 	private function where_greater($field,$value,$type,$table = ''){
	 		if (strlen($this->where) > 0) {
	 			if (strlen($table) > 1) {
	 				$this->where .= $type.' '.$table.'.'.$field.' > ? ';
	 			}else{
	 				$this->where .= $type.' '.$field.' > ? ';
	 			}
	 		}else{
	 			if (strlen($table) > 0) {
	 				$this->where = 'WHERE '.$table.'.'.$field.' > ? ';
	 			}else{
	 				$this->where = 'WHERE '.$field.' > ? ';
	 			}
	 		}
	 		$this->addParam([$value]);
			return $this;
	 	}

	 	public function and_where_greater($field,$value,$table = ''){
			return $this->where_greater($field,$value,'AND',$table);
	 	}

	 	public function or_where_greater($field,$value,$table = ''){
			return $this->where_greater($field,$value,'OR',$table);
	 	}

	 	private function where_greater_equal($field,$value,$type,$table = ''){
	 		if (strlen($this->where) > 0) {
	 			if (strlen($table) > 1) {
	 				$this->where .= $type.' '.$table.'.'.$field.' >= ? ';
	 			}else{
	 				$this->where .= $type.' '.$field.' >= ? ';
	 			}
	 		}else{
	 			if (strlen($table) > 0) {
	 				$this->where = 'WHERE '.$table.'.'.$field.' >= ? ';
	 			}else{
	 				$this->where = 'WHERE '.$field.' >= ? ';
	 			}
	 		}
	 		$this->addParam([$value]);
			return $this;
	 	}

	 	public function and_where_greater_equal($field,$value,$table = ''){
			return $this->where_greater_equal($field,$value,'AND',$table);
	 	}

	 	public function or_where_greater_equal($field,$value,$table = ''){
			return $this->where_greater_equal($field,$value,'OR',$table);
	 	}



	 	private function where_less($field,$value,$type,$table = ''){
	 		if (strlen($this->where) > 0) {
	 			if (strlen($table) > 1) {
	 				$this->where .= $type.' '.$table.'.'.$field.' < ? ';
	 			}else{
	 				$this->where .= $type.' '.$field.' < ? ';
	 			}
	 		}else{
	 			if (strlen($table) > 0) {
	 				$this->where = 'WHERE '.$table.'.'.$field.' < ? ';
	 			}else{
	 				$this->where = 'WHERE '.$field.' < ? ';
	 			}
	 		}
	 		$this->addParam([$value]);
			return $this;
	 	}

	 	public function and_where_less($field,$value,$table = ''){
			return $this->where_less($field,$value,'AND',$table);
	 	}

	 	public function or_where_less($field,$value,$table = ''){
			return $this->where_less($field,$value,'OR',$table);
	 	}

	 	private function where_less_equal($field,$value,$type,$table = ''){
	 		if (strlen($this->where) > 0) {
	 			if (strlen($table) > 1) {
	 				$this->where .= $type.' '.$table.'.'.$field.' <= ? ';
	 			}else{
	 				$this->where .= $type.' '.$field.' <= ? ';
	 			}
	 		}else{
	 			if (strlen($table) > 0) {;
	 				$this->where = 'WHERE '.$table.'.'.$field.' <= ? ';
	 			}else{
	 				$this->where = 'WHERE '.$field.' <= ? ';
	 			}
	 		}
			$this->addParam([$value]);
			return $this;
	 	}

	 	public function and_where_less_equal($field,$value,$table = ''){
			return $this->where_less_equal($field,$value,'AND',$table);
	 	}

	 	public function or_where_less_equal($field,$value,$table = ''){
			return $this->where_less_equal($field,$value,'OR',$table);
		}
		
		public function where_filter($filters = array()){
			$i = 0; 
			foreach($filters as $filter){
				if($i == 0 ) $this->where =  (strlen($this->where < 1) ? 'WHERE ' : (isset($filter['join']) ? $filter['join'] : ' AND ') ) .' '. (isset($filter['table']) ? $filter['table'].'.' : '').$filter['field'].' '.(isset($filter['sign']) ? $filter['sign'] : "=").' ? ';
				else  (isset($filter['join']) ? $filter['join'] : ' AND ').' '.(isset($filter['table']) ? $filter['table'].'.' : '').$filter['field'].' '.(isset($filter['sign']) ? $filter['sign'] : "=").' ?';
				if (is_numeric($filter['value'])) {	$this->string .= 'i';}else{ $this->string .= 's';}
				array_push($this->parameters, $filter['value']);
				$i++;
			}
			return $this;
    }
    
    public function add_condition($condition,$values){
      $this->where .= (strlen($this->where) < 1 )  ? 'WHERE '.$condition : $condition;
      $this->addParam($values);
      return $this;
    }

	 	public function offset($offset){
			$this->offset = ' OFFSET ?';
			$this->addParam([(int)$offset]);
			return $this;
	 	}

	 	public function limit($limit){
      $this->limit = ' LIMIT ?';
      $this->addParam([(int)$limit]);
			return $this;
	 	}

    public function order_by_desc($table,$field){
			$this->order = ' ORDER BY '.$table.'.'.$field.' DESC';
			return $this;
    }

    public function order_by_asc($table,$field){
			$this->order = ' ORDER BY '.$table.'.'.$field.' ASC';
			return $this;
		}
		
		public function order_rand(){
			$this->order = ' ORDER BY RAND()';
			return $this;
		}

    public function echoSql(){
			echo $this->sql.$this->join.$this->where.$this->order.$this->limit.$this->offset;
			echo $this->string;
      var_dump($this->parameters);
      echo mysqli_error($this->dbconn);
      return $this;
    }


	public function execute($option = 3){
		$this->sql = $this->sql.$this->join.$this->where.$this->order.$this->limit.$this->offset;
		$res = $this->dbconn->prepare($this->sql);
		echo mysqli_error($this->dbconn);
		
		if($res){
			count($this->parameters) > 0 && $res->bind_param($this->string, ...$this->parameters);
			$res->execute();
			$this->resetSql();
			if (!$res->execute()) {
				return array('message' => mysqli_error($this->dbconn), 'status' => false);
			}else{
				$select_record = $res->get_result();
				$data = array();
				$num =  $select_record->num_rows;
				if ($option == 1) {
					$data = $select_record->fetch_assoc();
				} else if($option == 2){
					$data = $select_record->fetch_all();
				}else if($option == 3){
					$data = $select_record->fetch_all(MYSQLI_ASSOC);
				}
				// echo mysqli_error($this->dbconn);
				if ($num >= 1) {
					return array('message' => 'Fetch Success', 'count'=>$num, 'data'=>$data,'status' => true);
				}else{
					return array('message' => mysqli_error($this->dbconn), 'status' => false);
				}
			}
		}else return array('message' => mysqli_error($this->dbconn), 'status' => false);	
	
	}

	
	public function exec(){
		$this->row = null;
		$this->rows = null;
		$this->count = 0;
		$this->sql = $this->sql.$this->join.$this->where.$this->order.$this->limit.$this->offset;
		try {
			$res = $this->dbconn->prepare($this->sql);
			if($res){
				
				count($this->parameters) > 0 && $res->bind_param($this->string, ...$this->parameters);
				$res->execute();
				$this->resetSql();
				if (!$res->execute()) {
					// throw GError::ThrowDBError("Mysql Error Error Processing Request". mysqli_error($this->dbconn));
					return false;
				}else{
					$select_record = $res->get_result();
					$num =  $select_record->num_rows;
					$data = $select_record->fetch_all(MYSQLI_ASSOC);
					if ($num > 0) {
						$this->count = $num;
						$this->rows  = $data;
						$this->row   = $this->rows[0];
						return $this;
					}else{
						return  $this;
					}
				}
			}else  $this;	
		} catch (\Throwable $th) {
			throw $th;
			return false;
		}
	}


	private  function resetSql()
	{
		$this->sql = '';
  	$this->where = '';
  	$this->join = '';
  	$this->limit =  '';
  	$this->order = '';
  	$this->offset = '';
		$this->string = '';
		$this->parameters = array();
	}

	public function insert($table,$data){
		$fields = array_keys($data);
		$string = '';
		$plc_holder = '';
		$values = array();
		for ($i=0; $i < sizeof($fields); $i++) { 
			if(is_numeric($data[$fields[$i]])) {$string .= 'i'; }else{ $string .= 's';}
			if ($i == (sizeof($fields)-1)) {$plc_holder .= '?';}else{ $plc_holder .= '?,'; }
			array_push($values,$this->dbconn->real_escape_string($data[$fields[$i]]));
		}
		$sql = " INSERT INTO $table (".implode(',',$fields).")  VALUES ( $plc_holder )";
		$prep_insert = $this->dbconn->prepare($sql);echo mysqli_error($this->dbconn);
		// echo mysqli_error($this->dbconn);
		if($prep_insert){
			$prep_insert->bind_param($string, ...$values);
			$prep_insert->execute();			
			echo mysqli_error($this->dbconn);
			if (!$prep_insert) {
				return false;
			}else{
				return $this->dbconn->insert_id != 0 ?  $this->dbconn->insert_id : true;
			}
		}else return  false;

	}
	public function delete($table,$condition)
	{
		$where = '';
		$string = '';
		$values = [];
		$clause = array_keys($condition);
		for ($i=0; $i < count($clause); $i++) { 
			$where = $i == 0 ? 'WHERE ' : '';
			$where  .= $clause[$i];
			if(is_numeric($condition[$clause[$i]])) {$string .= 'i'; }else{ $string .= 's';}
			array_push($values,$condition[$clause[$i]]);
			if ($i == (sizeof($clause)-1)) { $where .= '= ? '; }else{ $where .= '= ? AND '; }
		}
		$sql = 'DELETE FROM '.$table.' '.$where;
		$prep = $this->dbconn->prepare($sql);
		if($prep){
			$prep->bind_param($string, ...$values);
			$prep->execute();			
			echo mysqli_error($this->dbconn);
			if (!$prep) {
				return false;
			}else{
				return true;
			}
		}else return  false;
	}

	public function update($tablename,$data,$condition){
		$fields = array_keys($data);
		$string = '';
		$values = array();
		$conditions = '';
		for ($i=0; $i < sizeof($fields); $i++) { 
			$conditions .= $fields[$i];
			if(is_numeric($data[$fields[$i]])) {$string .= 'i'; }else{ $string .= 's';}
			if ($i == (sizeof($fields)-1)) { $conditions .= '= ? ';  }else{ $conditions .= '= ?,'; }
			array_push($values,$data[$fields[$i]]);
		}
		$clause = array_keys($condition);
		$where = '';
		for ($i=0; $i < count($clause); $i++) { 
			$where  .= $clause[$i];
			if(is_numeric($condition[$clause[$i]])) {$string .= 'i'; }else{ $string .= 's';}
			array_push($values,$condition[$clause[$i]]);
			if ($i == (sizeof($clause)-1)) { $where .= '= ? '; }else{ $where .= '= ? AND '; }
		}
		$sql = " UPDATE $tablename SET $conditions WHERE $where";
		try {
			$prep_update = $this->dbconn->prepare($sql);
			$prep_update->bind_param($string, ...$values);
			$prep_update->execute();
			if (!$prep_update) {
				return false;
			}else{
				return true;
			}
		} catch (\Throwable $th) {
			throw( new GCDBError($th,mysqli_error($this->dbconn)));
		}
	}

		public function createDb(){

		}

		public function createTable($tablename,$fields){

		}
	}
?>