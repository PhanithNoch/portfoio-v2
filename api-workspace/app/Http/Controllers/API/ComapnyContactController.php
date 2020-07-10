<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CompanyContact;

class ComapnyContactController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'company_contact',
            'model' => 'App\Model\CompanyContact', 
            'prefixId' => 'con'
        ];
    }
    
    public function getQuery(){
        return CompanyContact::query();
    }
    
    public function getModel(){
        return 'App\Model\CompanyContact';
    }

    public function getCreateRules(){
        return [
        ];
    }
}
