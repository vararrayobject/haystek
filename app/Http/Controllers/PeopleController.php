<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;

class PeopleController extends Controller
{
    function index(Request $request)
    {
        // dd($request->all());
        $peoples = json_decode((File::get(base_path('database/data/people.json'))));
        $chunkPeoples = collect($peoples)->slice($request->page * 3, 3);
       
        if($request->ajax()){
            // $subset = collect($peoples)->skip($request->page * 3)->take(3);
            // return collect($peoples)->slice($request->page, $request->page + 3);
            return $chunkPeoples;

        }
        return response()->view('welcome', compact('chunkPeoples', $chunkPeoples));
    }
}
