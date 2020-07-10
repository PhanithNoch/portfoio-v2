<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RestResource;
use App\Exceptions\CustomException;
use App\Http\Controllers\ResponseHandler;
use Illuminate\Support\Facades\Hash;

class UsersController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "users",
            "model" => "App\Model\User",
            "modelTranslate" => "App\Model\UserTranslation",
            "prefixId" => "usr",
            "prefixLangId" => "usr0t",
            "parent_id" => "users_id"
        ];
    }

    public function getQuery(){
        return User::query();
    }

    public function getModel(){
        return "App\Model\User";
    }
    
    public function getCreateRules(){
        return [
            "phone" => "required|unique:users,phone",
            
            "username" => "required|unique:users,username",
            "email" => "unique:users,email",
            "password" => "required"
            
        ];
    }

    public function beforeCreate(&$lstNewRecords){
        $userInfo = Auth::user();
        foreach ($lstNewRecords as $key => &$user) {
            $user["company_id"] = $userInfo["company_id"];
        }
    }

    public function getUpdateRules(){
        return [
            "id" => "required",
            "phone" => "max:15"
        ];
    }    
    
    public function userProfile(){
        $userInfo = Auth::user();
        return new RestResource($userInfo);
    }

    public function changePassword(Request $request){
        $data = $request->all(); 
 
        $user = User::findOrFail($data['id']);

        if(isset($data['old_password'])){
            //if old password isnot match, return error
            if(Hash::check($data['old_password'], $user['password'])){
                if(!isset($data["new_password"])){
                    throw new CustomException("Required new password value!", 0);
                }

                $updateData["id"] = $data['id'];
                $updateData['password'] = $data['new_password'];  
                $lstRecordUpdated = $this->upsert([$updateData]); 

                //return single record as resource
                return new RestResource($lstRecordUpdated[0]);
                
            }else{
                throw new CustomException("Invalid old password!!!", 0);
            } 
        }
        return ResponseHandler::clientError("Cannot change password!");
    }
}
