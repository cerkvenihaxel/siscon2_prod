<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DatePickerController extends Controller
{
    public function datePrueba(){
    $date = $request->input('date');
    $date = Carbon::parse($date)->format('Y-m-d');
    $art = $request->input('busqueda');

    

    // Do something with the date
    // for example:
    $data = DB::table('entrantes')->where('created_at', '<=', $date)->get();
    return view('your-view', compact('data', 'art'));
    }}
