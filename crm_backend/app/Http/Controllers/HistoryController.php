<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Model\Companies;
// use Carbon\Carbon;

class HistoryController extends Controller{
  
  public function getAllHisory(Request $request){
    return DB::table('history')
      ->join('invite', 'history.id_invite', '=', 'invite.id')
      ->join('users as emp', 'invite.id_emp', '=', 'emp.id')
      ->join('users as admin', 'invite.id_admin', '=', 'admin.id')
      ->join('companies as cmp', 'invite.id_cmp', '=', 'cmp.id')
      ->select(
        'history.id_invite',
        'history.time',
        'history.status',
        'emp.first_name',
        'emp.last_name',
        'admin.first_name as admin_first_name',
        'admin.last_name as admin_last_name',
        'cmp.name as company_name',
      )
      ->get();

      // ->Join('users as admin', 'companies.id_admin', '=', 'admin.id')
  }
}