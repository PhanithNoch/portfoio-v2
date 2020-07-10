<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Products;
use App\Model\ProductCategories;
use App\Services\Helper;
use App\Services\DatabaseGW;
use App\Model\Categories;
use App\Http\Resources\RestResource;

class ProductsController extends RestAPI
{
    public function getTableSetting(){
        return [
            'tablename' => 'products',
            'model' => 'App\Model\Products',
            'modelTranslate' => 'App\Model\ProductTranslation',
            'prefixId' => 'pro',
            'prefixLangId' => 'pro0t',
            'parent_id' => 'products_id'
        ];
    }
    
    public function getQuery(){
        return Products::query();
    }
    
    public function getModel(){
        return 'App\Model\Products';
    }   
    
    public function beforeCreate(&$lstNewRecords){
        # code logic here ...
    }
    
    public function afterCreate(&$lstNewRecords){
        // $this->createProductCategory($lstNewRecords);
    }
    
    public function beforeUpdate(&$lstNewRecords, $mapOldRecords=[]){
        // $this->createProductCategory($lstNewRecords);
    }
    
    public function afterUpdate(&$lstNewRecords){
        # code logic here ...
    }
    
    public function publicProduct(Request $req){
        try{
            $lstFilter = $req->all(); 
            $lstFilter['is_active'] = 1;
            return RestResource::collection(DatabaseGW::queryByModel($this->getQuery(), $lstFilter));
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
 
    /**
     * Method to create/delete product_category
     * @param array list products to check
     */
    private function createProductCategory(&$lstNewRecords){

        //get default category to create a default product_category if there are no category assign to product
        $mapCategoryType = Categories::where("is_default", 1)->get()->mapWithKeys(function ($item) {
                                return [$item['category_type'] => $item["id"]];
                            })->all();

        $cateController = new CategoriesController();
        $proCateCtrler = new ProductCategoryController();
        $lstCateIds2Del = array();

        $lstProIds2DelCate = array();
        
        //TODO: check field category_selected to delete/create product category
        //loop product to create product category
        foreach ($lstNewRecords as $index => $product) {
            if(!isset($product["type"])) continue;

            //if there are no default category for product type, create a new one for it
            if(!isset($mapCategoryType[$product["type"]])){
                $defCate = array("category_type" => $product["type"],
                                "is_default" => 1,
                                "langs" => array(
                                    "en_US" => array(
                                        "label" => "Uncategorized",
                                        "name" => "uncategorized"
                                    )
                                )
                            );
                $defCateCreated = DatabaseGW::updateOrCreate($defCate, $cateController->getTableSetting(), 0);

                //add new default category into map
                $mapCategoryType[$product["type"]] = $defCateCreated["id"];
            }
            $defCateid = $mapCategoryType[$product["type"]];

            //check to create a default product_category for product
            //if product doesnt has category_ids and category_selected, we need to create 
            //product category that linked product with default category
            if(!isset($product["category_selected"]) && !isset($product["category_ids"])){
                $proCate = $this->generateProductCatgory($product["id"], $defCateid);
                DatabaseGW::updateOrCreate($proCate, $proCateCtrler->getTableSetting(), $index);//create product_category for product
                continue;
            }

            $cateSelected   = isset($product["category_selected"]) ? $product["category_selected"] : "";
            $oldCate        = isset($product["category_ids"]) ? $product["category_ids"] : "";

            $lstCateSelectedIds = Helper::explode(",", $cateSelected);
            $lstOldCateIds      = Helper::explode(",", $oldCate);

            $cateIds2Del = array_diff($lstOldCateIds, $lstCateSelectedIds);
            $cateIdsNew = array_diff($lstCateSelectedIds, $lstOldCateIds);

            $lstCateIds2Del = array_merge($lstCateIds2Del, $cateIds2Del);

            $lstProIds2DelCate[] = $product["id"];

            //create new product category for product
            if(isset($cateIdsNew) && !empty($cateIdsNew)){

                //generate product_category
                foreach ($cateIdsNew as $indexCate => $cateid) {
                    $proCate = $this->generateProductCatgory($product["id"], $cateid);
                    DatabaseGW::updateOrCreate($proCate, $proCateCtrler->getTableSetting(), $index); //create product_category for product
                }

                //if product has an category, remove product_category that has relation with uncategorized
                $lstCateIds2Del[] = $defCateid;
            }
            
        }

        //delete product_category that user unselected
        if(isset($lstCateIds2Del) && !empty($lstCateIds2Del)){
            ProductCategories::whereIn("categories_id", $lstCateIds2Del)->whereIn("products_id", $lstProIds2DelCate)->delete();
        }
    }

    private function generateProductCatgory($proId, $cateId){
        return array("products_id" => $proId, "categories_id" => $cateId);
    }
}
