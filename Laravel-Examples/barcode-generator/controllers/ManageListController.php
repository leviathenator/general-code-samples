<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ManageListController extends Controller
{
    public function index(){
  
	}
	
	public function manageLists(Request $request, $type){
		
		$logged = session('init-token');
		
		if(empty($logged)){
			
			return redirect('/');
		}
		
		$tble = 'rxa_'.$type;
		
		$del = DB::delete('delete from '.$tble);
		
		$list = $request->all();

		if(!empty($list)){
			
			unset($list['_token']);
			
			foreach($list as $key => $val){
				
				DB::insert('insert into '.$tble.' (name) values (?)', [$val]);
			}
		
			$jsonResponse = (object)[
				'errcode' => false, 
				'msg' => 'Your '.$type.' list has been updated'
			];
			
		}else{
			
			$jsonResponse = (object)[
				'errcode' => true, 
				'msg' => 'You must add items to your list.'
			];
		}
	    
	    if($request->ajax()) {
			return response()->json($jsonResponse);		
		}
	    
	}
}
