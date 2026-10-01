<?php

namespace App\Http\Controllers;

class TrendPageController extends Controller
{
    public function index()
    {
        if (view()->exists('trend.index')) {
            return view('trend.index');
        }
        if (view()->exists('trend')) {
            return view('trend');
        }

        abort(404, 'View trend belum dibuat.');
    }
}
