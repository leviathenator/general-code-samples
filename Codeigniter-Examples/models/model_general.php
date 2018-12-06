<?php

class Model_General extends CI_Model {
		
	 function __construct()
	    {
		  // Call the Model constructor
		  parent::__construct();
		  		  
	    }

	
	function simple_select($options){

		$this->db->select($options['needle']);
		
		if(is_array($options['fork'])){
			
			foreach($options['fork'] as $key => $val){
				
				if(!empty($key) && !empty($val)){
					$this->db->where($key, $val);
				}
			}
			
		}else{
			return FALSE;
		}
		
		if(!empty($options['sortcol'])){
			$this->db->sort_by( $options['sortcol'], (!empty($options['dir']) ? $options['dir'] : 'ASC') );
		}
		
		if(!empty($options['haystack'])){
			$sql = $this->db->get($options['haystack'], false);
		}else{
			return FALSE;
		}

		if($sql->num_rows() > 0 ){
			return ( !empty($needletype) && $needletype === 'array' ? $sql->result_array() : $sql->result() );
		}else{
			return FALSE;
		}
	}
	
	/**
	 * Inserts a single row from an array. 
	 * Returns single Primary Key ID, unless $returnvalue is FALSE. Alternately returns the object array 
	 * 
	 *
	 * @access	public
	 * @param	string $table_name, array $array_values, boolean $return_value
	 * @return	integer || array
	 */
	function return_after_insert($table_name, $array_values, $return_value = TRUE){
		
		if(!empty($table_name) && !empty($array_values)){
			
			$this->db->insert($table_name, $array_values);
			$this->db->select_max('id');
			$data = $this->db->get($table_name);
			
			if($data->num_rows() > 0){
				$data = $data->result();
				return ($return_value ? $data[0]->id : $data->result() );
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
		
	}

}