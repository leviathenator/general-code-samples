<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ListController extends Controller
{
	
	public function index($field = 'client_name', $dir = 'desc', $listtotal = 15){
		
		$logged = session('init-token');
		
		if(empty($logged)){
			
			return redirect('/');
		}
	    
	    $market_list = DB::select('select * from rxa_markets');
	    $action_list = DB::select('select * from rxa_actions');
	    $vendor_list = DB::select('select * from rxa_vendors');
	    
	    $field = ($field === 'client' ? 'client_name' : 'client_name');
	    $field = ($field === 'date' ? 'datetime' : 'client_name');
	    $field = ($field === 'group' ? 'group_id' : 'client_name');
	    $field = ($field === 'barcode' ? 'barcode_id' : 'client_name');
	    
	    $lists = DB::table('rxa_lists')
	    ->orderBy($field, $dir)
	    ->paginate($listtotal);
	    
	    $dir = (!empty($dir) && $dir === 'desc' ? 'asc' : 'desc');
	    
	    return view('sections.lists', compact('lists','market_list','action_list','vendor_list', 'dir'));
	    
	}
	
	public function search(Request $request, $listtotal = 15){
		
		$logged = session('init-token');
		
		if(empty($logged)){
			
			return redirect('/');
		}
	    
	    $market_list = DB::select('select * from rxa_markets');
	    $action_list = DB::select('select * from rxa_actions');
	    $vendor_list = DB::select('select * from rxa_vendors');
	    
	    $lists = DB::table('rxa_lists')
	     			->where('client_name', 'like', '%'.$request->input('search').'%')
	     			->orWhere('group_id', 'like', '%'.$request->input('search').'%')
	     			->orWhere('barcode_id', 'like', '%'.$request->input('search').'%')
	    			->paginate($listtotal);
	    			
	    $dir = (!empty($dir) && $dir === 'desc' ? 'asc' : 'desc');
	    
	    return view('sections.lists', compact('lists','market_list','action_list','vendor_list', 'dir'));
	    
	}
	
	public function openListItem($id){
		
		$logged = session('init-token');
		
		if(empty($logged)){
			
			return redirect('/');
		}
	    
	    $market_list = DB::select('select * from rxa_markets');
	    $action_list = DB::select('select * from rxa_actions');
	    $vendor_list = DB::select('select * from rxa_vendors');
	    
	    $list = DB::table('rxa_lists')
						->where('id', '=', $id)
	    				->get();
	   
	    $file_list = DB::table('rxa_files')
		    				->select('filename')
		    				->where('idlist', $id)
		    				->get();	
	    
	   	    
	    $popovers = (object)[
			'client_name' => 'Enter the name of the client requesting, e.x. “Darci Cianci”',
			'vendor' => 'Select the vendor creating the barcodes',
			'market' => 'Select the market the barcodes will be loaded for',
			'action' => 'Replace file logic: In order to completely replace existing barcodes with new barcodes, the first row in the “replace” UNL file must reflect, for example: RAUXA~NA~S~<GroupID>~ZZZZZZZZZZZZZZZZZZZZ <br /> Note: there must be 20 “Z” listed in the Barcode Group ID section',
			'group_id' => 'This is a client provided ID that ties into their discounting system and should be entered without modification, example: HHSE100OFSPBASE0117',
			'barcode_id' => 'An identifier for the barcode set, example: Job number (VNA12345), promo description (175OFF), sometimes client provided (SEQ12yr). The Barcode ID may need to be shortened to ensure the SUM of the Barcode ID and the Group ID is less than or equal to 20 characters (can never shorten the Group ID).',
			'split_num' => 'First file will contain the number of records indicated in this field, the second file will contain the remainder',
			'total' => 'Total number of records to generate.'	
		];

		return view('sections.barcodeform', compact('popovers','market_list','action_list','vendor_list', 'list', 'file_list'));
	}

}
