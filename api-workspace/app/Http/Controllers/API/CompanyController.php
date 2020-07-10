<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Company;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RestResource;
use App\Model\AvailableLanguages;

class CompanyController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'company',
            'model' => 'App\Model\Company',
            'modelTranslate' => 'App\Model\CompanyTranslation',
            'prefixId' => 'com',
            'prefixLangId' => 'com0t',
            'parent_id' => 'company_id'
        ];
    }
    
    
    public function getQuery(){
        return Company::query();
    }
    
    
    public function getModel(){
        return 'App\Model\Company';
    }
    
    public function getCreateRules(){
        return [
           "phone" => "required"
        ];
    }

    public function getInfo(){
        $userInfo = Auth::user();
        return $this->show($userInfo["company_id"]); 
    }
    
    public function companyDomain(){ 
        $record = Company::query()->where("company_type" , "owner")->firstOrFail();
        //return single record as resource
        return new RestResource($record);
    }

    public function publicCompany(Request $request){

        $lstFilter = $request->all();

        $with = [];
        $with[] = 'availableLanguages';

        $query = Company::query()
                    ->where("company_type" , "owner");

        if(isset($lstFilter["with"])){
            $lstWiths = explode(',', $lstFilter["with"]);

            foreach ($lstWiths as $index => $value) {
                $with[] = $value;
            }
            
        } 
        $query->with($with);
        $record = $query->get();
        //return single record as resource
        return new RestResource($record);
    }

    public function getAllConfig(){

        $userInfo = Auth::user();
        $company = Company::query()->where("id" , $userInfo["company_id"])->firstOrFail();
        $availableLang = AvailableLanguages::where("company_id", $company["id"])->get();

        $record = [
            "com_info" => $company,
            "available_langs" => $availableLang
        ];
        
        // return $record;
        return new RestResource(collect($record));
    }
}
