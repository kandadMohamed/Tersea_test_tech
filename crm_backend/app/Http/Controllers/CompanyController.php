<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Model\Companies;
// use Carbon\Carbon;

class CompanyController extends Controller{

  public function getAllCompanies(Request $request){
    
    $companies = DB::table('companies')
                  ->leftJoin('invite', 'companies.id', '=', 'invite.id_cmp')
                  ->leftJoin('users as admin', 'companies.id_admin', '=', 'admin.id')
                  ->leftJoin('users as emp', 'emp.id', '=', 'invite.id_emp')
                  ->select(
                    'companies.*', 
                    'invite.status',
                    'admin.first_name as first_name', 
                    'admin.last_name as last_name',
                    // DB::raw("count(*) AS nb_emp"),
                    DB::raw("SUM(IF(invite.status = 'valide', 1, 0)) AS nb_emp")
                  )
                  // ->selectRaw("
                  //   SUM(CASE WHEN invite.status= 'active' THEN 1 ELSE 0 END) AS active_games,
                  // ")
                  ->groupBy(
                    'companies.id',
                    'companies.name',
                    'companies.id_admin',
                    'companies.deleted_at',
                    'companies.created_at',
                    'first_name',
                    'last_name',
                    'invite.status'
                  );

    if($request->name !== ''){
      $companies = $companies->where('companies.name', 'LIKE', '%'.$request->name.'%');
    }

    return $companies->get();
  }

  public function addCompany(Request $request){
    DB::table('companies')
      ->insert([
          'name' => $request->name,
          'id_admin' => $request->id_admin
        ]);    

    $objetoRequest = new Request();
    $objetoRequest->setMethod('POST');
    $objetoRequest->request->add([
        'name' => $request->searchCompany
    ]);
    return $this->getAllCompanies($objetoRequest);
  }

  public function editCompany(Request $request){
    DB::table('companies')
              ->where('id', $request->id)
              ->update(['name' => $request->name]);
        
    $objetoRequest = new Request();
    $objetoRequest->setMethod('POST');
    $objetoRequest->request->add([
        'name' => $request->searchCompany
    ]);
    return $this->getAllCompanies($objetoRequest);
  }

  public function deleteCompany(Request $request){
    DB::table('companies')
      ->where('id', '=', $request->id)
      ->update(['deleted_at' => Carbon::now()]);

    DB::table('invite')
      ->where('status', '=', 'invite')
      ->where('id_cmp', '=', $request->id)
      ->update(['status' => 'refused']);

    return response()->json([
      'status' => 'success'
    ]);
  }

}