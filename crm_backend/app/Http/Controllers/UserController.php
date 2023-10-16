<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeeInvite;
// use Laravel\Sanctum;
// use Illuminate\Support\Facades\ss
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
  public function getEmployeesAccounts(Request $request){
    $user = DB::table('users as emp')
      ->join('invite', 'emp.id', '=', 'invite.id_emp' )
      ->join('companies as cmp', 'invite.id_cmp', '=', 'cmp.id' )
      ->join('users as admin', 'invite.id_admin', '=', 'admin.id' )
      ->select(
        'emp.id', 
        'emp.first_name', 
        'emp.last_name', 
        'invite.status', 
        'cmp.name as company_nom',
        'admin.last_name as admin_last_name',
        'admin.first_name as admin_first_name',
      )
      ->where('emp.profile', 'emp')
      ->get();
    return $user;
  }

  public function login(Request $request){

    $user = DB::table('users')
      ->where('email', $request->email)
      ->first();

    $token;

    if($user){

      if($user->profile === 'admin'){

        if($user->password === $request->password){

          // $token = $user->createToken('token')->plainTextToken;
          // $cookie = cookie('jwt', $token, 60*24*10);
          
          return response()
            ->json([
              'status' => 'success',
              'user' => $user
            ]);
        }
        return response()
          ->json([
            'status' => 'error',
            'message'=>'password incorrect',
          ]);

      }else{
        $Employee = DB::table('users')
          ->join('invite as emp', 'users.id', '=', 'emp.id_emp')
          ->where('email', $request->email)->first();
          // return $Employee;
        
        switch ($Employee->status) {
          case 'invite':{
            return response()
              ->json([
                'status' => 'error',
                'message' => 'Your Account Not Valide check your email '
              ]);
            break;
          }
          case 'refused':
            return response()
              ->json([
                'status' => 'error',
                'message' => 'Your Invitation Is Refused'
              ]);
            break;  
          default:
            if($Employee->password === $request->password){

              // $token = $user->createToken('token')->plainTextToken;
              // $cookie = cookie('jwt', $token, 60*24*10);

              return response()
                ->json([
                  'status' => 'success',
                  'user' => $Employee
                ]);
            }
            return response()
              ->json([
                'status' => 'error',
                'message'=>'password incorrect',
              ]);
            break;
        }

      }

    }else{
      return response()
        ->json([
          'status' => 'error',
          'message'=>'Email incorrect',
        ]);
    }
    
  }

  public function createAccount(Request $request){
    $response;
    $user = DB::table('users')
      ->where('email', $request->email)
      ->first();

      // $token = $user->createToken('token')->plainTextToken;
      // return $request->email;
    
    if($user){
      if($user->profile === 'admin'){
        $response = response()
          ->json([
            'status' => 'error',
            'message'=>'This Account Already existe as administrator',
          ]);
      }else{
        if($request->profile === 'admin'){
          $response = response()
            ->json([
              'status' => 'error',
              'message'=>'This Account Already as employee',
            ]);
        }else{

          $Employee = DB::table('users')
          ->join('invite as emp', 'users.id', '=', 'emp.id_emp')
          ->where('email', $request->email)->first();

          $emp_status = $Employee->status;
  
          switch ($emp_status) {
            case 'invite':
              $response = response()
              ->json([
                'status' => 'error',
                'message'=>'This Account Already Invite But not Accepted',
              ]);
              break;
            
            case 'valide':
              $response = response()
              ->json([
                'status' => 'error',
                'message'=>'This Account Already existe as employee',
              ]);
              break;
            
            default:
              $response = response()
              ->json([
                'status' => 'error',
                'message'=>'This Account Already Invite but Invitation is refuse',
              ]);
              break;
          }
        }
      }

    }else{
      if($request->profile === 'admin'){
        DB::table('users')->insert([
          'first_name'=> $request->first_name,
          'last_name'=> $request->last_name,
          'password'=> $request->password,
          'email'=> $request->email,
          'id_admin'=> $request->id_admin,
          'profile'=> 'admin'
        ]);
        
        $response = response()
        ->json([
          'status' => 'success',
          'message'=>'good',
        ]);
      }else{
        
        DB::table('users')->insert([
          'first_name'=> $request->first_name,
          'last_name'=> $request->last_name,
          'password'=> '',
          'email'=> $request->email,
          'profile'=> 'emp',
        ]);

        $id_emp = DB::table('users')
          ->where('email', $request->email)
          ->select('id')
          ->first();

        DB::table('invite')->insert([
          'id_admin'=> $request->id_admin,
          'id_emp'=> $id_emp->id,
          'id_cmp'=> $request->company,
        ]); 

        $invite = DB::table('invite')
        ->where('id_emp', '=', $id_emp->id)
        ->first();

        DB::table('history')
        ->insert([
          'id_invite'=> $invite->id,
          'status'=> 'invite',
        ]); 
        
        $Emp = DB::table('users as emp')
        ->join('invite', 'emp.id', '=', 'invite.id_emp' )
        ->join('companies as cmp', 'invite.id_cmp', '=', 'cmp.id' )
        ->join('users as admin', 'invite.id_admin', '=', 'admin.id' )
        ->select(
          'emp.id', 
          'emp.first_name', 
          'emp.last_name', 
          // 'invite.status', 
          'cmp.name as company_name',
          'admin.last_name as admin_last_name',
          'admin.first_name as admin_first_name',
        )
        ->where('emp.email', $request->email)
        ->first();

        Mail::to($request->email)
          ->send(new EmployeeInvite((array)$Emp));
        
        $objetoRequest = new Request();
        $objetoRequest->setMethod('POST');
        $objetoRequest->request->add([
            // 'name' => $request->searchCompany
        ]);

        $response = response()
        ->json([
          'status' => 'success',
          'users'=> $this->getEmployeesAccounts($objetoRequest),
        ]);
      }
    }
    return $response;
  }
  
  public function refuseInvite(Request $request){
    DB::table('invite')
      ->where('id_emp', $request->id)
      ->update(['status' => $request->status]);
      
      $objetoRequest = new Request();
      $objetoRequest->setMethod('POST');
      return $this->getEmployeesAccounts($objetoRequest);
  }

  public function getEmployeeInvite(Request $request){
    $id_emp_invite = $request->id;

    $emp_invite = DB::table('invite')
    ->where('id_emp', $id_emp_invite)
    ->first();
    
    if($emp_invite){
      // return $emp_invite;
      if($emp_invite->status === 'invite'){
        return response()
              ->json([
                'status' => 'success',
                'user' => $emp_invite
              ]);
        
      }elseif($emp_invite->status === 'valide'){
        return response()
              ->json([
                'status' => 'error',
                'message' => 'your Account Already Valide'
              ]);
      }else{
        return response()
              ->json([
                'status' => 'error',
                'message' => 'your Invite is Refused'
              ]);
      }
    }
    return response()
            ->json([
              'status' => 'error',
              'message' => 'you dont Invite'
            ]);
  }
  
  public function valideAccount(Request $request){
    // return $request;
    DB::table('users')
      ->where('id', $request->id)
      ->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'password' => $request->password
      ]);

    DB::table('invite')
      ->where('id_emp', $request->id)
      ->update([
        'status' => 'valide',
      ]);

    $invite = DB::table('invite')
      ->where('id_emp', '=', $request->id)
      ->first();

    DB::table('history')
      ->insert([
        'id_invite'=> $invite->id,
        'status'=> 'valide',
      ]); 
    return response()
    ->json([
      'status' => 'error',
      'message' => 'your Account Valide'
    ]);
  }
}
