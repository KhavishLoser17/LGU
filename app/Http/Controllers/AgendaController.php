<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function agenda(){
        return view('minutes.agenda');
    }
     public function test(){
        return view('minutes.test');
    }
}
