<?php

class Financial_Statements extends MY_Controller {

	function __construct() {

		parent::__construct();

		$this -> load -> model('model_riskreview', 'risk');
		$this -> load -> model('model_general', 'gen');
		$this -> load -> helper('app_forminput_helper');
		$cookie = $this->session->userdata('rpt_cookie');
		
		(isset($cookie['idbwr']) ? $this->data['idbwr'] = $cookie['idbwr'] : '');
		(isset($cookie['idrpt']) ? $this->data['idrpt'] = $cookie['idrpt'] : '');
	}
	
	public function manage(){
		
		$this->checkValidationCred();
		
		$seg_arr = $this -> uri -> uri_to_assoc(3);
		
		if(empty($seg_arr['relationship'])){
			$this->HandleResponse("No IDrelat", true);
		}else{
			$idrelat = $seg_arr['relationship'];
		}
		
		if( !$this->checkOnlyInt($idrelat) ){
			$this->HandleResponse("URL Query Error", true);
		}
		
		if( empty($seg_arr['act']) ){
			$action = 'add';
		}else{
			$action = $seg_arr['act'];
		}
		
		switch($action){
			
			case 'edit' :

				if( empty($seg_arr['statement']) ){
					$this->HandleResponse("No IDfs", true);
				}else{
					$id = $seg_arr['statement'];
				}
				
				if( !$this->checkOnlyInt($id) ){
					$this->HandleResponse("URL Query Error", true);
				}
			
				if(!empty($_POST)){
					
					foreach($_POST as $key => $val){
					
	                        if(strpos($key, 'xtrainc:') > -1){
	                        	$xtra_arr = explode(':', $key);
								if(!empty($val)){
									$xtra[$xtra_arr[1]][$xtra_arr[2]] = $val;
								}
								unset($_POST[$key]);
	                        }
						
						// Only pulling any Newly added Debt Srvice Lines to add to the table. All others are readonly and added by
						// the trigger when the laons are created. 
						
							if(strpos($key, 'debtserv:') > -1){
	                        	$debtserv_arr = explode(':', $key);
								$debtserv[$debtserv_arr[1]][$debtserv_arr[2]] = $val;
								unset($_POST[$key]);
	                        }
	                        
	                        if(strpos($key, 'debtservnew:') > -1){
	                        	$debtservnew_arr = explode(':', $key);
								$debtservnew[$debtservnew_arr[1]][$debtservnew_arr[2]] = $val;
								unset($_POST[$key]);
	                        }
					}
					
					$this->db->where('idfs', $id);
					$this->db->delete('####');
					
					if(!empty($xtra)){
						foreach($xtra as $x){
							
							//if(!empty($x['val'])){
								$x['idfs'] = $id;
								$this->db->insert('####', $x);
							//}
						}
						
					}
					
					$this->db->where('usr_created', 1);
					$this->db->where('idrel', $idrelat);
					$this->db->delete('####');

					
					if(!empty($debtserv)){
						
						foreach($debtserv as $ds){
							
							if(!empty($ds['loan_bal']) && !empty($ds['month_payment'])){
								$ds['idrel'] = $idrelat;
								$ds['usr_created'] = 1;
								$this->db->insert('####', $ds);
							}
						}
					
					}
					
					if(!empty($debtservnew)){
						foreach($debtservnew as $ds){
							if(!empty($ds['loan_bal']) && !empty($ds['month_payment'])){
								$ds['idrel'] = $idrelat;
								$ds['usr_created'] = 1;
								$this->db->insert('####', $ds);
							}
						}
					}
					
					$_POST['idrelat'] = $idrelat;

					$this->gen->update_sanitize_from_direct_post($id, '####');

					$this->HandleResponse("The new financial statement has been updated.", false);
					
				}else{

					if($bwrs = $this-> risk -> gt_borrower_ids_f_idrel($idrelat)){
						
						foreach($bwrs as $key => $val){
							$bwrarr[] = $val->id;
						}
						
						if(!empty($bwrarr)){
							
							$data['debtserv'] = $this->risk->gt_fs_debtserv_f_idbwr($bwrarr, $idrelat);
						}
						
						$data['rel'] = $this->risk->gt_fs_single($id);
					}
										
					$data['fstypes'] = $this->risk->gt_fs_types_defaults();
					$data['idrelat'] = $idrelat;
					$data['idfs'] = $id;
					
					$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/financial-statements/fs', $data, true));
				}
			
				break;
				
			case 'remove' :
				
				if( empty($seg_arr['relationship']) ){
					$this->HandleResponse("No IDrel", true);
				}else{
					$idrelat = $seg_arr['relationship'];
				}
				
				if( empty($seg_arr['statement']) ){
					$this->HandleResponse("No IDbur", true);
				}else{
					$idstmnt = $seg_arr['statement'];
				}
				
				if( !$this->checkOnlyInt($idrelat) || !$this->checkOnlyInt($idstmnt)){
					$this->HandleResponse("URL Query Error", true);
				}

				$this->db->where('id', $idstmnt);
				$this->db->delete('####');
				
				$this->db->where('idfs', $idstmnt);
				$this->db->delete('####');
				
				$this->db->where('idfs', $idstmnt);
				$this->db->where('usr_created', 1);
				$this->db->delete('####');
				
				$this->HandleResponse("Financial Statement Removed", false, $idrelat);
			
				break;
				
			case 'add' :
			default :
			
				if(!empty($_POST)){
			
					foreach($_POST as $key => $val){

						if(strpos($key, 'xtrainc:') > -1){
							$xtra_arr = explode(':', $key);
						if(!empty($val)){
							$xtra[$xtra_arr[1]][$xtra_arr[2]] = $val;
						}
							unset($_POST[$key]);
						}
						
						// Only pulling any Newly added Debt Srvice Lines to add to the table. All others are readonly and added by
						// the trigger when the loans are created. 
						
						if(strpos($key, 'debtserv:') > -1){
		                    $debtserv_arr = explode(':', $key);
							$debtserv[$debtserv_arr[1]][$debtserv_arr[2]] = $val;
							unset($_POST[$key]);
		                }
		                        
		                if(strpos($key, 'debtservnew:') > -1){
		                    $debtservnew_arr = explode(':', $key);
							$debtservnew[$debtservnew_arr[1]][$debtservnew_arr[2]] = $val;
							unset($_POST[$key]);
		                }
					}

					$_POST['idrelat'] = $idrelat;
					$idfs = $this -> gen -> return_after_insert('####', $_POST, TRUE);
					
					if(!empty($xtra)){
						foreach($xtra as $x){
							$x['idfs'] = $idfs;
							$this->db->insert('####', $x);
						}
					}
					
					$this->db->where('usr_created', 1);
					$this->db->where('idrel', $idrelat);
					$this->db->delete('####');
					
					if(!empty($debtserv)){
						foreach($debtserv as $ds){
							if(!empty($ds['loan_bal']) && !empty($ds['month_payment'])){
								$ds['idrel'] = $idrelat;
								$ds['usr_created'] = 1;
								$this->db->insert('####', $ds);
							}
						}
					}
					
					if(!empty($debtservnew)){
						foreach($debtservnew as $dsn){
							
							if(!empty($dsn['loan_bal']) && !empty($dsn['month_payment'])){
								$dsn['idrel'] = $idrelat;
								$dsn['usr_created'] = 1;
								$this->db->insert('####', $dsn);
							}
						}
					}
					
					$this->HandleResponse("Your financial statement type has been added.", false);

				}else{
					
					if($bwrs = $this-> risk -> gt_borrower_ids_f_idrel($idrelat)){
						//print_r($bwrs);
						foreach($bwrs as $key => $val){
							$bwrarr[] = $val->id;
						}
						
						if(!empty($bwrarr)){
							$data['debtserv'] = $this->risk->gt_fs_debtserv_f_idbwr($bwrarr, $idrelat);
						}
					}
					
					$data['fstypes'] = $this->risk->gt_fs_types_defaults();
					$data['idrelat'] = $idrelat;

					$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/financial-statements/fs', $data, true));
				}
			
				break;
		}

	}
	
	private function cashflow(){
		
		$this->checkValidationCred();
		
		$seg_arr = $this -> uri -> uri_to_assoc(3);
		
		if(empty($seg_arr['relationship'])){
			$this->HandleResponse("No IDrelat", true);
		}else{
			$idrelat = $seg_arr['relationship'];
		}
		
		if( !$this->checkOnlyInt($idrelat) ){
			$this->HandleResponse("URL Query Error", true);
		}
		
		if(empty($seg_arr['itms'])){
			$this->HandleResponse("No Itms", true);
		}
		
		$itms = explode('-', $seg_arr['itms']);
		$itms = array_filter($itms);
		
		$data['fs'] = $this->risk->gt_fs_cashflow($itms);

		if($bwrs = $this-> risk -> gt_borrower_ids_f_idrel($idrelat)){

			foreach($bwrs as $key => $val){
				$bwrarr[] = $val->id;
			}
			
			if(!empty($bwrarr)){
				$data['debtserv'] = $this->risk->gt_fs_debtserv_f_idbwr($bwrarr, $idrelat);
			}
		}

		$data['action'] = 'add';

		$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/cashflow', $data, true));
	}

	
	private function add_statement_pane(){

		$this->checkValidationCred();
		
		$seg_arr = $this -> uri -> uri_to_assoc(3);
		
		if(empty($seg_arr['idbwr'])){
			$this->HandleResponse("No IDfs", true);
		}
		
		if(!$this->checkOnlyInt($seg_arr['idbwr']) ){
			$this->HandleResponse("URL Query Error", true);
		}
		
		$data['action'] = 'add';
		$data['rel']['debtserv'] = $this->risk->gt_fs_debtserv_f_idbwr($seg_arr['idbwr']);
		$data['fs_types'] = $this->risk->gt_fs_types_defaults();
		$data['idbwr'] = $seg_arr['idbwr'];
		
		$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/fs_stmnt_pane', $data, true));
	}

	private function view_statement_pane(){

		$this->checkValidationCred();
		
		$seg_arr = $this -> uri -> uri_to_assoc(3);
		
		if(empty($seg_arr['idfs'])){
			$this->HandleResponse("No IDfs", true);
		}
		
		if(!$this->checkOnlyInt($seg_arr['idfs']) ){
			$this->HandleResponse("URL Query Error", true);
		}
		$idbwr = $this->data['idbwr'];
		$idrpt = $this->data['idrpt'];
		
		$data['action'] = 'edit';
		$data['fs_types'] = $this->risk->gt_fs_types_defaults();
		$data['idfs'] = $seg_arr['idfs'];
		$data['idbwr'] = $idbwr;
		
		$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/fs_stmnt_pane', $data, true));
	}

	
	private function save_fs(){
		
		$this->checkValidationCred();
		
		if(!empty($_POST)){

			$seg_arr = $this -> uri -> uri_to_assoc(3);
		
			if(empty($seg_arr['act']) ){
				$this->HandleResponse("No Action", true);
			}
			
			foreach($_POST as $key => $val){
					
				if(strpos($key, 'xtrainc_') > -1){
					$xtra_arr = explode('_', $key);
					
					if(!empty($val)){
						$xtra[$xtra_arr[1]][$xtra_arr[2]] = $val;
					}
				
					unset($_POST[$key]);
				}
				
				// Only pulling any Newly added Debt Srvice Lines to add to the table. All others are readonly and added by
				// the trigger when the laons are created. 
				
				if(strpos($key, 'debtserv-') > -1){

                    $debtserv_arr = explode('-', $key);
					$debtserv[$debtserv_arr[1]][$debtserv_arr[2]] = $val;
					unset($_POST[$key]);
                }
		
            }

			if($seg_arr['act'] == 'edit'){
				
				if(!$this->checkOnlyInt($seg_arr['idfs']) ){
					$this->HandleResponse("No IDfs", true);
				}
				
				$this->db->where('id', $seg_arr['idfs']);
				$this->db->update('####', $_POST);
				
				
				$this->db->where('idfs', $seg_arr['idfs']);
				$this->db->delete('####');
				
				if(!empty($xtra)){
					foreach($xtra as $x){
						
						if(!empty($x['val'])){
							$x['idfs'] = $seg_arr['idfs'];
							$this->db->insert('####', $x);
						}
					}
				}

				if(!empty($debtserv)){
						
					$this->db->where('usr_created', 1);
					$this->db->where('idfs', $seg_arr['idfs']);
					$this->db->where('idbwr', $this->data['idbwr']);
					$this->db->delete('####');
					
					foreach($debtserv as $ds){
						
						if(!empty($ds['loan_bal']) && !empty($ds['month_payment'])){
							$ds['idfs'] = $seg_arr['idfs'];
							$ds['idbwr'] = $this->data['idbwr'];
							$this->db->insert('####', $ds);
						}
					}
				}
				
				$this->HandleResponse("Your financial statement type has been edited.", false);
				
			}else{
				
				$this->db->insert('####', $_POST);
				$this->db->select_max('id');
				$newID = $this->db->get('####')->result();
				$newID = $newID[0]->id;
				if(!empty($xtra)){
					foreach($xtra as $x){
						$x['idfs'] = $newID;
						$this->db->insert('####', $x);
					}
				}
				if(!empty($debtserv)){
					foreach($debtserv as $ds){
						
						if(!empty($ds['loan_bal']) && !empty($ds['month_payment'])){
							$ds['idfs'] = $newID;
							$ds['idbwr'] = $this->data['idbwr'];
							$this->db->insert('####', $ds);
						}
					}
					
				}
				$this->HandleResponse("Your financial statement type has been added.", false);
			}
		}
	}

	
	private function fs_open_popup(){
		$this->checkValidationCred();
		
		$seg_arr = $this -> uri -> uri_to_assoc(3);
		
		if(empty($seg_arr['idfs'])){
			$this->HandleResponse("No IDfs", true);
		}
		
		if(!$this->checkOnlyInt($seg_arr['idfs']) ){
			$this->HandleResponse("URL Query Error", true);
		}
		
		if(empty($seg_arr['act']) ){
			$this->HandleResponse("No Action", true);
		}
		$data['action'] = $seg_arr['act'];
		
		$ckie = $this->session->userdata('rpt_cookie');
		
		$data['idrpt'] = $ckie['idrpt'];
		$data['idbwr'] = $ckie['idbwr'];
		
		if($data['action'] == 'edit'){

			if(empty($seg_arr['idfs'])){
				$this->HandleResponse("No IDfs", true);
			}
			$data['idfs'] = $seg_arr['idfs'];
			
		}else{
			
			$data['debtserv'] = $this->gt_fs_debtserv_f_idbwr($data['idrpt']);
		}
		
		$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/fs_manage', $data, true));
	}


	
	private function add_cashflow(){
		
		$this->checkValidationCred();
		
		$seg_arr = $this -> uri -> uri_to_assoc(3);
		
		if(empty($seg_arr['idbwr'])){
			$this->HandleResponse("No IDfs", true);
		}
		
		if(!$this->checkOnlyInt($seg_arr['idbwr']) ){
			$this->HandleResponse("URL Query Error", true);
		}
		
		if(empty($seg_arr['itms'])){
			$this->HandleResponse("No Itms", true);
		}
		
		$itms = explode('-', $seg_arr['itms']);
		$itms = array_filter($itms);
		
		$data['fs'] = $this->risk->gt_fs_cashflow($itms);
		$data['debtserv'] = $this->risk->gt_fs_debtserv_f_idbwr($this->data['idbwr']);
		
		$data['action'] = 'add';
		
		$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/fs_cashflow', $data, true));
	}


	
	private function bwr_fs_detail(){
		
		$this->checkValidationCred();
		
		$seg_arr = $this -> uri -> uri_to_assoc(3);
		
		if(!$this->checkOnlyInt($seg_arr['idbwr']) ){
			$this->HandleErrorFlash('URL Query Error', TRUE);
			redirect('relationships/relatelist');
		}

		if(empty($seg_arr['idbwr'])){
			$this->HandleErrorFlash('No relationship identification', TRUE);
			redirect('relationships/relatelist');
		}
		
		$data['rel'] = $this -> risk -> gt_fs_all_f_idbwr($seg_arr['idbwr']);
		if(!empty($seg_arr['instance']) && $seg_arr['instance'] == 'risepanel'){
			$this->HandleResponse("", false, $this->load->view('/snippets/reuse/fragments/detailbox_bwr_fs', $data, true));
		}else{
			$this->HandleResponse("", false, $this->load->view('/snippets/reuse/fragments/detailbox_bwr_fs', $data, true));
		}
	}

	
	private function delete_statement(){
		
		$this->checkValidationCred();
		
		$seg_arr = $this -> uri -> uri_to_assoc(3);
		
		if(!$this->checkOnlyInt($seg_arr['idfs']) ){
			$this->HandleResponse("URL Query Error", true);
		}
		
		$this->db->where('id', $seg_arr['idfs']);
		$this->db->delete('####');
		
		$this->db->where('idfs', $seg_arr['idfs']);
		$this->db->delete('####');
		
		$this->db->where('idfs', $seg_arr['idfs']);
		$this->db->delete('####');
		
		$this->HandleResponse("Financial Statement was deleted", false);
	}
}