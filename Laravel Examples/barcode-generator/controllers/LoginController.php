<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index(){

	    return view('sections.loginform');
    }

    public function validateUser(Request $request){

	    $pass = $request->input('p');

	    $old_pass_chk = DB::select('select val from rxa_settings where type = "passcode"');

	    if (Hash::check( $pass, $old_pass_chk[0]->val )) {

		    session(['init-token' => true]);

		    $jsonResponse = (object)[
					'errcode' => false,
				];

		}else{

			$jsonResponse = (object)[
					'errcode' => true,
					'msg' => 'Your password did not match our records.',
				];
		}

		return response()->json($jsonResponse);

    }
}
