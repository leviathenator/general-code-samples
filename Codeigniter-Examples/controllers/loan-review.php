<?php

class Loans extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> model('model_riskreview', 'risk');
		$this -> load -> model('model_general', 'gen');
		$this -> load -> helper('app_forminput_helper');
		$cookie = $this->session->userdata('rpt_cookie');
		(isset($cookie['idbwr']) ? $this->data['idbwr'] = $cookie['idbwr'] : '');
		(isset($cookie['idrpt']) ? $this->data['idrpt'] = $cookie['idrpt'] : '');
		(isset($cookie['idbk']) ? $this->data['idbk'] = $cookie['idbk'] : '');
	}


	function toolbox(){

		$this->checkValidationCred();

		$seg_arr = $this -> uri -> uri_to_assoc(3);

		if(empty($seg_arr['relationship'])){
			$this->HandleResponse('No IDrel', TRUE);
		}

		if(empty($seg_arr['loan'])){
			$this->HandleResponse('No IDln', TRUE);
		}

		if(!$this->checkOnlyInt($seg_arr['loan']) ){
			$this->HandleResponse('URL Query Error', TRUE);
		}

		if( $data['rel'] = $this -> risk -> gt_loan_single($seg_arr['loan'])){

			$data['loantypes'] = $this->risk->gt_ln_types_formatted();
			$data['rating_rule'] = $this->risk->gt_bank_rating_system($this->data['idbk']);
			$data['idrelat'] = $seg_arr['relationship'];

			$this->HandleResponse("", false, $this->load->view('/snippets/reuse/toolboxes/loan', $data, true));
		}else{
			$this->HandleResponse("No Loan ID or does not exist", true);
		}
	}

	function save_loan(){

		$this->checkValidationCred();

		$seg_arr = $this -> uri -> uri_to_assoc(3);

		if(empty($seg_arr['idln'])){
			$this->HandleResponse('No IDln', true);
		}

		if(!$this->checkOnlyInt($seg_arr['idln']) ){
			$this->HandleResponse('URL Query Error', true);
		}

		if(!empty($_POST)){

			$this->db->where('id', $seg_arr['idln']);
			$this->db->update('####', $_POST);

			// A trigger on the app_loans table is updating loangroups, but doens't
			// take into account loan participants
			// TODO: review triggers in app_loans to either add loan participants
			// in the trigger, or replace the trigger
			$this->risk->update_associated_loan_groups($seg_arr['idln']);

			$this->HandleResponse('This loan has been saved!', false);
		}else{
			$this->HandleResponse('No POst DAta', true);
		}
	}

	function manage(){

		$this->checkValidationCred();

		$seg_arr = $this -> uri -> uri_to_assoc(3);

		if(empty($seg_arr['relationship'])){
			$this->HandleResponse('No IDidrel', true);
		}else{
			$idrel = $seg_arr['relationship'];
		}

		if( empty( $seg_arr['report'] ) && empty( $this->data['idrpt'] ) ){
			$this->HandleResponse('No IDidrpt', true);
		}else{
			$idrpt = (!empty($seg_arr['report']) ? $seg_arr['report'] : $this->data['idrpt']);
		}

		if(!empty($_POST)){

			foreach($_POST as $key => $val){

				if(strpos($key, 'activeloan:') > -1){

					$aloankey = explode(':', $key);
					$activeloans[$aloankey[1]][$aloankey[2]] = $val;
				}

			}

			$err = FALSE;
			$ermsg = '';

			foreach($activeloans as $key => $val){

				$tempid = $val['tempid'];
				unset($val['tempid']);
				// Pull out the items that will be aded to the borrower table and unset them.
				$borrower['name'] = $val['borrower_name'];
				$borrower['dixat'] = $val['dixat'];
				$borrower['dixat_suf'] = $val['dixat_suf'];
				$borrower['dixat_tot'] = $val['dixat_tot'];
				$borrower['idrpt'] = $idrpt;
				unset($val['borrower_name']);
				unset($val['dixat']);
				unset($val['dixat_suf']);
				unset($val['dixat_tot']);

				// Check if loan doesn't exists in this current report
				if($ln = $this->risk->check_if_no_loan_in_report($val['number'], $idrpt)){

					// Add loan
					$idln = $this->gen->return_after_insert('####', $val);

					// Add loan to the existing Report
					$this->db->insert('####', array('idln' => $idln, 'idrpt' => $idrpt));

					// Is this borrower attached to this report yet?
					if($bwr = $this->risk->check_if_borrower_in_report($borrower['dixat'], $idrpt)){

						// Borrower IS attached to this report.
						$idbrw = $bwr[0]->id;

						// Add loan to the existing Borrower
						$this->db->insert('####', array('idbwr' => $idbrw, 'idln' => $idln));

						// No need to add to a relationship because if the borrower already exists and
						// is attached to the report then it has already been added to the relationship.


					}else{

						// Borrower does not exist, so we insert the new borrower and attach the row ID to the loan
						$idbrw_new = $this->gen->return_after_insert('###', $borrower);

						// Add loan to the new Borrower
						$this->db->insert('####', array('idbwr' => $idbrw_new, 'idln' => $idln));

						// Add the new borrower to this relationship.
						$this->db->insert('####', array('idbwr' => $idbrw_new, 'idrel' => $idrel));

						// Add the new relationship to this report
						$this->db->insert('####', array('idrel' => $idrel, 'idrpt' => $idrpt));


					}

					$this->db->where('id', $tempid);
					$this->db->delete('####');

				}else{
					$err = TRUE;
				}

			}

			if($err){
				$this->HandleResponse('One or more loans already existed in this report and could not be added to this relationship. ', true);
			}else{
				$this->HandleResponse('Loan(s) added', false);
			}

		}else{

			$data['bwrs'] = $this->risk->gt_bwr_formatted($idrel);
			$data['idrelat'] = $idrel;

			$this->db->select('####.dixat');
			$this->db->join('####', 'app_relationship_borrower.idbwr = app_borrower.id');
			$this->db->where('####.idrel !=', $idrel);
			$this->db->where('####.idrpt', $idrpt);
			$unavailable_bwrs = $this->db->get('####');

			if($unavailable_bwrs->num_rows() > 0){
				foreach($unavailable_bwrs->result() as $k => $v){
					$unavailables[] = $v->dixat;
				}
			}

			$this->db->select('id, number, borrower_name, current_balance, dixat, dixat_suf, dixat_tot, officer, bk_risk_rating, interest_rate, monthly_payment, orig_amount, orig_date');
			$this->db->where('idrpt', $idrpt);

			if(!empty($unavailables)){
				$this->db->where_not_in('dixat', $unavailables);
			}

			$lns = $this->db->get('####');

			if($lns->num_rows() > 0){
				$data['sample_loans'] = $lns->result_array();
			}

			$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/loans/add', $data, true));

		}

	}

	function managesingle(){

		$this->checkValidationCred();

		$seg_arr = $this -> uri -> uri_to_assoc(3);

		if(empty($seg_arr['relationship'])){
			$this->HandleResponse('No IDidrel', true);
		}else{
			$idrel = $seg_arr['relationship'];
		}

		if( empty( $seg_arr['report'] ) && empty( $this->data['idrpt'] ) ){
			$this->HandleResponse('No IDrpt', true);
		}else{
			$idrpt = (!empty($seg_arr['report']) ? $seg_arr['report'] : $this->data['idrpt']);
		}

		if(!empty($_POST)){

			$idbwr = $_POST['idbwr'];
			unset($_POST['idbwr']);

			$idln = $this->gen->return_after_insert('####', $_POST);

			// Add loan to the existing Borrower
			$this->db->insert('####', array('idbwr' => $idbwr, 'idln' => $idln));

			// Add loan to the existing Report
			$this->db->insert('####', array('idln' => $idln, 'idrpt' => $idrpt));

			$this->HandleResponse('Loan added', false);

		}else{

			$data['bwrs'] = $this->risk->gt_bwr_formatted($idrel);
			$data['idrel'] = $idrel;

			$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/loans/add-loan-single', $data, true));
		}

	}

	function riskrating(){

		$this->checkValidationCred();

		$seg_arr = $this -> uri -> uri_to_assoc(3);

		if(empty($seg_arr['loan'])){
			$this->HandleResponse('No IDln', TRUE);
		}else{
			$id = $seg_arr['loan'];
		}

		if(empty($seg_arr['relationship'])){
			$this->HandleResponse('No IDrel', TRUE);
		}else{
			$idrelat = $seg_arr['relationship'];
		}

		if(!$this->checkOnlyInt($id) ){
			$this->HandleResponse('URL Query Error', TRUE);
		}

		if(!empty($_POST)){

			$this->db->where('id', $id);
			$this->db->update('app_loans', $_POST);

			$data['newrr'] = $_POST['assessed_risk_rating'];

			$this->HandleResponse("Risk Rating was updated", false, $data);

		}else{
			$this->HandleResponse("URL ERROR", false);
		}


		//$data['industries'] = $this->risk->gt_industry_types();
	}

	function get_temp_loans(){

		$this->checkValidationCred();

		$seg_arr = $this -> uri -> uri_to_assoc(3);

		if(empty($seg_arr['borrower'])){
			$this->HandleResponse('No IDidbwr', true);
		}else{
			$idbwr = $seg_arr['borrower'];
		}

		$this->db->select('dixat');
		$this->db->where('id', $idbwr);
		$sql = $this->db->get('####');
		if($sql->num_rows() > 0){
			$dixat = $sql->result();

			$this->db->select('id, number, borrower_name');
			$this->db->where('dixat',$dixat[0]->dixat);
			$this->db->where('idrpt',$this->data['idrpt']);
			$sql2 = $this->db->get('####');

			if($sql2->num_rows() > 0){
				$lns = $sql2->result();

			}
		}

		if(!empty($lns)){
			$data['lns'] = $lns;
		}else{
			$data = NULL;
		}
		$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/loans/loans-select', $data, true));
	}

	function participants(){

		$this->checkValidationCred();

		$seg_arr = $this -> uri -> uri_to_assoc(3);

		if(empty($seg_arr['relationship'])){
			$this->HandleResponse('No IDrel', TRUE);
		}else{
			$idrelat = $seg_arr['relationship'];
		}

		if(empty($seg_arr['loan'])){
			$this->HandleResponse('No IDln', TRUE);
		}else{
			$idln = $seg_arr['loan'];
		}

		if(empty($seg_arr['action'])){
			$this->HandleResponse('No IDaction', TRUE);
		}else{
			$action = $seg_arr['action'];
		}

		switch($action){

			case 'edit' :

				if(empty($seg_arr['participant'])){
					$this->HandleResponse('No IDlnpart', TRUE);
				}else{
					$idlnpart = $seg_arr['participant'];
				}

				// Grab the borrower id
				$this->db->select('idbwr');
				$this->db->where('idln', $idln);
				$sql = $this->db->get('####');

				if($sql->num_rows() > 0) {
					$data['idbwr'] = $sql->result()[0]->idbwr;
				}

				if(!empty($_POST)){

					$temp = $_POST;
					$temp['idln'] = $idln;

					$this->db->where('id', $idlnpart);
					$this->db->update('####', $temp);

					$this->risk->update_associated_loan_groups($idln);

					$data['idrelat'] = $idrelat;
					$data['idln'] = $idln;

					$this->HandleResponse("Loan participant has been edited", false, $data);

				}else{

					$rel = $this->risk->gt_loan_participant_single($idlnpart);

					$data['rel'] = $rel[0];
					$data['idlnpart'] = $idlnpart;
					$data['idrelat'] = $idrelat;
					$data['idln'] = $idln;

					$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/loans/loan-participant', $data, true));
				}

				break;

			case 'remove' :

				if(empty($seg_arr['participant'])){
					$this->HandleResponse('No IDlnpart', TRUE);
				}else{
					$idlnpart = $seg_arr['participant'];
				}

				$this->db->where('id', $idlnpart);
				$this->db->delete('####');

				/// Count particiapnts and if 0 then update the #### table.
				if($ispart = $this->risk->gt_loan_participants_f_idln($idln)){

					$this->db->where('id', $idln);
					$this->db->update('####', array('####' => 'Y'));

				}else{

					$this->db->where('id', $idln);
					$this->db->update('####', array('####' => 'N'));
				}

				$this->risk->update_associated_loan_groups($idln);

				$data['idrelat'] = $idrelat;

				$this->HandleResponse("Loan participant has been removed", false, $data);

				break;

			case 'add' :

				if(!empty($_POST)){
			//print_r($_POST);
					$temp = $_POST;
					$temp['idln'] = $idln;

					$this->db->insert('app_loan_participants', $temp);

					$this->db->where('id', $idln);
					$this->db->update('app_loans', array('####' => 'Y'));

					$this->risk->update_associated_loan_groups($idln);

					$data['idrelat'] = $idrelat;

					$this->HandleResponse("Loan participant has been added", false, $data);

				}else{

					$this->db->select('idbwr');
					$this->db->where('idln', $idln);
					$sql = $this->db->get('####');

					if($sql->num_rows() > 0) {
						$data['idbwr'] = $sql->result()[0]->idbwr;
					}

					$data['idrelat'] = $idrelat;
					$data['idln'] = $idln;
					//$data['industry'] = $this->risk->gt_industry_formatted();
					$this->HandleResponse("", false, $this->load->view('/snippets/reuse/popups/loans/loan-participant', $data, true));
				}

				break;
		}


	}
}
