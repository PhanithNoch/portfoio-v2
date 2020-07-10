<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductCategoryController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'product_categories',
            'model' => 'App\Model\ProductCategories', 
            'prefixId' => 'cate'
        ];
    }
    
    public function getQuery(){
        return Categories::query();
    }
    
    public function getModel(){
        return 'App\Model\ProductCategories';
    }
    
    public function getCreateRules(){
        return [
            "products_id" => "required",
            "categories_id" => "required"
        ];
    }
    
    public function getUpdateRules(){
        return [
            'id' => 'required'
        ];
    }
}
