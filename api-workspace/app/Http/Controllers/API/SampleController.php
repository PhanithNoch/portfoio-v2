<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 

class SampleController extends RestAPI
{
    public function getTableSetting(){
        return [
            "tablename" => "database_table_name",
            "model" => "\path\\to\model",
            "modelTranslate" => "\path\\to\model_translate",
            "prefixId" => "table_prefix_id",
            "prefixLangId" => "table_translate_prefix_id",
            "parent_id" => "column_parent_table_in_translate"
        ];
    }

    public function getQuery(){
        return SampleModel::query();
    }

    public function getModel(){
        return "path\ to\model";
    }
    
    public function getCreateRules(){
        return [
           "phone" => "required"
        ];
    }

    public function getUpdateRules(){
        return [
            "id" => "required",
            "phone" => "numeric|phone_number|max:15"
        ];
    }

    public function beforeCreate(&$lstNewRecords){
        # code logic here ...
    }
 
    public function afterCreate(&$lstNewRecords){
        # code logic here ...
    }

    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){
        # code logic here ...
    }
 
    public function afterUpdate(&$lstNewRecords){
        # code logic here ...
    }
}
