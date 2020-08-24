<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UjicobaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function ujicoba(Request $request)
    {
        $unggah_file	= $request->file('unggah_file');
		$name			= time();	
		$extension		= $unggah_file->getClientOriginalExtension();
		$newname		= $name . '.' .$extension;
		$path			= $request->file('unggah_file')->storeAs('public', $newname);
		
		return $path; 
    }
}
