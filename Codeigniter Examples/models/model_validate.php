<?php

class Model_Validate extends CI_Model {
	
	function __construct(){
	  // Call the Model constructor
	  parent::__construct();
	  
	}
	
	/** VALIDATE USER EXISTS
	* Checks users email to identify if that user exists
	* returns TRUE if exists, otherwise FALSE
	*
	* @access	public
	* @return	boolean
	*/
	function verify_user_exists($email){
		
		$this->db->where('email', $email);
		$sql = $this->db->get('#####');
		return ($sql->num_rows() > 0 ? TRUE : FALSE);
	 	
	}
	
	/** Get User ID
	*
	* @access	public
	* @return	primary ID
	*/
	function get_user_id($email){
		
		$this->db->select('id');
		$this->db->where('email', $email);
		$sql = $this->db->get('#####');
		if($sql->num_rows() > 0){
			$sql = $sql->result();
			return $sql[0]->id;
		}else{
			return FALSE;
		}
	 	
	}
	
	/** VALIDATE USER/PASSWORD COMBO
	* Checks users email and password
	* returns TRUE if exists, otherwise FALSE
	*
	* @access	public
	* @return	boolean
	*/
	function verify_user_password($email, $pass){

	 	$this->db->select('
		 	#####.id as init_v_boot, 
	 		#####.date_added, 
	 		#####.f_name, 
	 		#####.l_name, 
	 		#####.email, 
	 		#####.last_active, 
	 		#####.spark_on_login,
	 		#####.god, 
	 		#####.super, 
	 		#####.editor, 
	 		#####.edit, 
	 		#####.del, 
	 		#####.view
	 	');
		$this->db->join('#####', '#####.id = #####.idusr');
		$this->db->where('#####.email', $email);
		$this->db->where('#####.init_v_pass', $pass);
		$this->db->where('#####.active', 'Y');
		$sql = $this->db->get('#####');
	       
		return ($sql->num_rows() > 0 ? $sql->result() : FALSE);
	 }
	
	//////////////////*********///////////////////////
	// Retreive One Single Email FROM Users
	function retrieve_one_email($user) {
		
		$this->db->select('id,full_name, email_address, password');
		$this->db->where('email_address =', $user);
		$mySQL = $this->db->get('#####');
		
		if($mySQL->num_rows() > 0) {
			foreach($mySQL->result() as $row) {
				$data[] = $row;	
			}
			return $data;
		}else{
			return FALSE;
		}
	}


	
	//////////////////*********///////////////////////
	// Set Cookie of All User Data 
	function get_permissions($user, $persist = false){
		
		$sql = $this->db->query('
			SELECT 
				#####.id, 
				#####.l_name,
				#####.f_name, 
				#####.email_address, 
				#####.pass_is_temp,
				#####.active
			FROM
				#####
			WHERE 
				#####.id = '. $user);
		if($sql->num_rows() > 0 ){
			
			$data['user'] = $sql->result_array();
			$sql = $this->db->query('
				SELECT 
					#####.id_tool,
					#####.edit,
					#####.del,
					#####.view,
					#####.god,
					#####.super
				FROM
					#####
				WHERE 
					#####.id_user = '. $user 
			);

			if($sql->num_rows() > 0 ){
				$data['permissions'] = $sql->result_array();
			}
			
		}
		
		if($persist){
			$sql = $this->db->query('UPDATE ##### SET ##### = 1 WHERE id = '. $user);
		}
		
		if(!empty($data)){
			return $data;
		}else{
			return FALSE;
		}

	}
	
	/** Initial Install Check
	* Does a DB check to see if any users exists.
	*
	* @access	public
	* @return	boolean
	*/
	function chck_if_install(){
		
		$sql = $this->db->get('#####');
		return ($sql->num_rows() > 0 ? TRUE : FALSE);
		//return FALSE;
	}
	

	
	//////////////////*********///////////////////////
	// NOT IN USE AT THIS TIME... POSSIBLY IN FUTURE
	
	function _reset_pass($email){
		
		$new = random_string('alnum', 7);
		$hash = hash_pass($new);
		$this->db->query('UPDATE ##### SET password = "'. $hash .'", ##### = 1 WHERE ##### LIKE "%'.$email.'%"');
		return $new;
		
	}
	
	function reset_pass_to_temp($id,$new_password) {
		
		$data = array('#####' => $new_password, '#####' => 1);
		$this->db->update('#####', $data, array('id' => $id));
		return TRUE;		
	}



}