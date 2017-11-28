<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExampleController extends Controller
{

    public function example() {
        $res = DB::select('select * from test');
        var_dump($res);
    }

}