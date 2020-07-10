<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\SiteNav;
use App\Services\DatabaseGW;
use App\Http\Resources\RestResource;

class SiteNavController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'site_nav',
            'model' => 'App\Model\SiteNav',
            'modelTranslate' => 'App\Model\SiteNavTranslation',
            'prefixId' => 'nav',
            'prefixLangId' => 'nav0t',
            'parent_id' => 'site_nav_id'
        ];
    }
    
    public function getQuery(){
        return SiteNav::query();
    }
    
    public function getModel(){
        return 'App\Model\SiteNav';
    }
    
    public function getCreateRules(){
        return [];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }
    
    public function publicIndex(Request $request){
        try{
            $model = $this->getQuery(); 
            $filters = ["with" => "subMenus", 
                        "site_nav_id" => null,
                        "order_col" => "ordering",
                        "order_by" => "asc"];

            $lstRecords = DatabaseGW::queryByModel($model, $filters);
            return RestResource::collection($lstRecords);
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
}
