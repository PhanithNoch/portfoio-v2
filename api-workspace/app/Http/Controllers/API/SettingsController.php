<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'settings',
            'model' => 'App\Model\Settings',
            'prefixId' => 'sett'
        ];
    }
    
    public function getQuery(){
        return Settings::query();
    }
    
    public function getModel(){
        return 'App\Model\Settings';
    }
    
    public function getCreateRules(){
        return [
            'name' => 'required'
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }
}
