<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReporteArticulosController extends Controller
{
    //
    public function handleDateRange(Request $request)
{

    $daterange = $request->input('daterange');
    $dates = explode(' - ', $daterange);
    $start_date = Carbon::parse($dates[0])->format('Y-m-d');
    $end_date = Carbon::parse($dates[1])->format('Y-m-d');

 
    $data = DB::table('entrantes')->whereBetween('date_column', [$start_date, $end_date])->get();
    return view('informesReportes', compact('data', 'start_date', 'end_date'));
}
}
