<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimplexController extends Controller
{
    public function index() {
        return view('home');
    }

    public function input(Request $request) {

        $var = $request->var;
        $cons = $request->cons;
        return view('input', [
        'var' => $var,
        'cons' => $cons
        ]);
    }

    public function hitung(Request $request) {


        return view('hasil');
    }
}
