<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UpdatePassController extends Controller
{
    public function index(){
	    
    }
    
    public function managePass(Request $request){
	    
	    $logged = session('init-token');
		
		if(empty($logged)){
			return redirect('/');
		}
	    
	    $new_pass = $request->input('newP');
	    $old_pass = $request->input('oldP');
	    $conf_pass = $request->input('confP');
	    
	    $old_pass_chk = DB::select('select val from rxa_settings where type = "passcode"');
	    if (Hash::check( $old_pass, $old_pass_chk[0]->val )) {
		    
		   $hash = Hash::make($new_pass);
		   DB::update('update rxa_settings set val = "'.$hash.'" where type = "passcode" ');
		   
		    $jsonResponse = (object)[
				'errcode' => false, 
				'msg' => 'Your password was updated'
			];
			
		}else{
			
			$jsonResponse = (object)[
				'errcode' => true, 
				'msg' => 'Ooops, There was an issue updating the password. Please make sure that your old password was correct.'
			];
		}

		if($request->ajax()) {
			return response()->json($jsonResponse);		
		}

    }
}
