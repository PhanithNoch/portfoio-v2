<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Photos;

class PhotoController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'photos',
            'model' => 'App\Model\Photos', 
            'prefixId' => 'photo'
        ];
    }
    
    public function getQuery(){
        return Photos::query();
    }
    
    public function getModel(){
        return 'App\Model\Photos';
    }

    public function getCreateRules(){
        return [
            "photo" => "required"
        ];
    }

    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    }
}
