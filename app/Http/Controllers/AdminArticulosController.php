<?php 
namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class AdminArticulosController extends Controller
{
    public function dateRangePicker(Request $request)
    {
        $dateRange = $request->get('daterange');
        list($start_date, $end_date) = explode(' - ', $dateRange);
        $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->toDateString();
        $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->toDateString();

        // your other code here

        return view('your_view',compact('start_date','end_date'));
    }
}
