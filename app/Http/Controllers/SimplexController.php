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
        if ($request->metode == 'simplex') {
            return view('hasil');
        } else{
            function hitungKombinasi($n, $m) {
                if ($m == 0 || $m == $n) {
                    return 1;
                } elseif ($m > $n) {
                    return 0;
                } else {
                    return hitungKombinasi($n - 1, $m - 1) + hitungKombinasi($n - 1, $m);
                }
            }

            return view('aljabar',[

            ]);
        }

    }
}
