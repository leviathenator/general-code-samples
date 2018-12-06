<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessFiles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class GenerateController extends Controller
{

    public function index(Request $request){

		$logged = session('init-token');

		if(empty($logged)){
			return redirect('/');
		}

		if(!empty($request)){

			$list = $request->all();

			Log::error($list['client_name']);

			dispatch( new ProcessFiles($list['client_name'], $list['vendor'], $list['market'], $list['action'], $list['group_id'], $list['barcode_id'], $list['total'], $list['split_num'] ) );

			//Log::error('After Displatch');

			$inital_filename = $list['vendor'].'_'.date('Ymd_Hi');

			$jsonResponse = (object)[
					'errcode' => false,
					'filename' => $inital_filename
			];


			//return response()->json($jsonResponse);

		}else{

			$jsonResponse = (object)[
				'errcode' => true,
				'msg' => 'You must add items to your list.'
			];
		}

		return response()->json($jsonResponse);

	}


	public function check_for_files($filename){

		$directory =date('Y').'/'.date('m').'/'.date('d').'/';

		$id = DB::table('rxa_lists')
			->select('*')
			->where('file_init', $filename)
			->get();

		if(!empty($id[0])){

			$newfile = $id[0]->file_init;

			if(Storage::disk('local')->has('public/'.$directory.$newfile.'.txt')){

				$files = DB::table('rxa_files')
					->select('filename')
					->where('idlist', $id[0]->id)
					->get();

				if(!empty($files[0])){

					$jsonResponse = (object)[
						'errcode' => false,
						'files' => $files
					];

				}else{

					$jsonResponse = (object)[
						'errcode' => true,
					];
				}

			}else{

				$jsonResponse = (object)[
					'errcode' => true,
				];
			}
		}else{

			$jsonResponse = (object)[
				'errcode' => true,
			];
		}

		return response()->json($jsonResponse);

	}



	public function check_code_size($gid, $bid, $total){

		$barcodelen = strlen(trim($bid));
		$totallen = strlen(trim($total));

		$get_last = DB::table('rxa_lists')
    				->select('last_num')
    				->where([
					    ['group_id', '=', $gid],
					    ['barcode_id', '=', $bid],
					])
    				->orderBy('datetime', 'desc')
    				->get();

		if(!empty($get_last[0]) && !empty($get_last[0]->last_num)){

			$start = $get_last[0]->last_num+1;
			$startlen = strlen(trim($start));

			$newtotal = $start+$total;
			$newtotallen = strlen(trim($newtotal));

			$combo = $barcodelen+$newtotallen;

			if($combo > 20){

				$decrease = $combo-20;

				$jsonResponse = (object)[
					'errcode' => true,
					'msg' => 'System cannot generate '.$total.' codes for this <Barcode ID>. Try changing the <Barcode ID> ID.',
					'field' => 'group'
				];

			}else{

				$jsonResponse = (object)[
					'errcode' => false,
				];

			}

		}else{

			$combo = $barcodelen+$totallen;

			if($barcodelen+$totallen > 20){

				$decrease = $combo-20;

				$jsonResponse = (object)[
					'errcode' => true,
					'msg' => 'Generating '.$total.' codes will cause your character count to exceed the 20 character limit.Your <Barcode ID> is currently at {'.$barcodelen.'} characters. Try decreasing your <Barcode ID> by {'.$decrease.'} characters.',
					'field' => 'group'
				];

			}else{

				$jsonResponse = (object)[
					'errcode' => false,
				];

			}
		}

		return response()->json($jsonResponse);

	}

}
