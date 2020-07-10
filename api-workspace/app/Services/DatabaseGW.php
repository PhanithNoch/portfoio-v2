<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Exceptions\CustomException;

class DatabaseGW
{
    /**
     * Method to query dynamic record
     * 
     * prefix for where condition
     * - "whereraw_fieldname" it means search value by find_in_set
     * - "g__fieldname" it means search with condition greater than
     * - "ge__fieldname" it means search with condition greater than or equals
     * - "l__fieldname" it means search with condition less than
     * - "le__fieldname" it means search with condition less than or equals
     * - "year__fieldname" it means search record in year by fieldname
     * - "month__fieldname" it means search record in month by fieldname
     * 
     * @param App\Model\ModelName $modelString   Model String path
     * @param array $lstFilters array to filter query
     * @return array result query records 
     */
    public static function queryByModel($model, $lstFilters = []){
 
        $withObject = [];
        $withCountObj = [];
        $limit = 50;
        $orderCol = "created_date";
        $orderby = "desc";
        
        //added filter query before get data
        foreach ($lstFilters as $colName => $value) {
 
            //get all additional objects present in model
            if($colName == "with"){
                $withObject = explode(",", $value);
                continue;
            }

            //get all with count obj
            if($colName == "with_count"){
                $withCountObj = explode(",", $value);
                continue;
            }

            if($colName == 'page') continue;


            if($colName == 'order_by') {
                $orderby = $value;
                continue;
            }
            if($colName == 'order_col') {
                $orderCol = $value;
                continue;
            }
            if($colName == 'limit'){
                $limit = $value;
                continue;
            }

            //to check if column need check condition with whereraw
            //the request will has prefix "whereraw_"
            if(strpos($colName, 'whereraw') !== false){

                if(!isset($value) || empty($value)){
                    continue;
                }
                $colName = str_replace("whereraw_", "", $colName);

                if(strpos($value, ",") === false){  
                    $model->whereRaw("find_in_set('".$value."', $colName)");
                }else{
                    $lstVal = explode(",", $value);

                    foreach($lstVal as $index => $val){
                        $model->orWhereRaw("find_in_set('".$val."', $colName)");
                    } 
                }
                continue;
            }

            //for query by checking if relationship existence
            if(strpos($colName, 'has__') !== false){
                $colName = str_replace("has__", "", $colName);
                $model->has($colName, '>', 0);
                continue;
            }

            //for query year
            if(strpos($colName, 'year__') !== false){
                $colName = str_replace("year__", "", $colName);
                $model->whereYear($colName, $value);
                continue;
            }

            //for query month
            if(strpos($colName, 'month__') !== false){
                $colName = str_replace("month__", "", $colName);
                $model->whereMonth($colName, $value);
                continue;
            }

            //if value is string null, we change it to use null variable
            if(!isset($value) || $value == 'null'){
                $model->where($colName, null);

            }else 
            //if there are no comma in value, it means we need to search by whole value
            if(strpos($value, ",") === false){ 
                //check with condition greater than
                if(strpos($colName, 'g__') !== false){
                    $colName = str_replace("g__", "", $colName);
                    $model->where($colName, '>', $value);
                }else 
                //check with condition greater  than or equals
                if(strpos($colName, 'ge__') !== false){
                    $colName = str_replace("ge__", "", $colName);
                    $model->where($colName, '>=', $value);
                }else 
                //check with condition less than
                if(strpos($colName, "l__") !== false){
                    $colName = str_replace("l__", "", $colName);
                    $model->where($colName, '<', $value);
                }else
                //check with condition less than or equals
                if(strpos($colName, "l__") !== false){
                    $colName = str_replace("le__", "", $colName);
                    $model->where($colName, '<=', $value);
                }
                //default condition equals
                else{
                    $model->where($colName, $value);
                }
                
            }
            //if there are more than one value, we switch to use whereIn
            //comma identify that it has multiple value
            else{
                $lstVal = explode(",", $value);
                $model->whereIn($colName, $lstVal);
            }
        } 
         
        if(!empty($withObject)){
            foreach ($withObject as $index => $withName) {
                $model->with($withName);
            }
        }

        if(!empty($withCountObj)){
            foreach ($withCountObj as $index => $withName) {
                $model->withCount($withName);
            }
        }

        $model->orderBy($orderCol, $orderby);
 
        $lstResults = $model->paginate($limit)->appends(Request::except('page'));
        return $lstResults;
    }

    /**
     * Method to update or create a records
     * @param object $record    record to be create/update
     * @param array $config     the table config setting
     * @param int $key          key to merge with id to advoid duplicate
     * @param boolean $isAnonymous|false    option to create/update without auth
     */
    public static function updateOrCreate($record, $config, $key = 0, $isAnonymous=false){
        $resObj = [];
        
        if (!empty($config) && !empty($record) ) {
            $recordId = "";

            DB::beginTransaction();
            try {

                // get dynamic model form config
                $className = $config['model'];
                $Model = new $className;

                // if langs empty not create and update translation
                if(isset($record['langs'])) {
                    $langsData = $record['langs'];
                    unset($record['langs']);
                }

                $isUpdate = isset($record['id']);
                
                // set system fields to record
                self::generateSysFields($record, $isUpdate, $isAnonymous);
                 
                // generate record id for insert
                $record['id'] = $isUpdate ? $record['id']: self::generateId($config['prefixId'].$key);

                //if it is update transaction, we will do all check permission and data owner
                // if ($isUpdate) {

                //     //get record from database to check if there are existed record in database
                //     $recordDB = $Model::where('id', $record['id'])->first();

                //     //if there are no record in database, return error
                //     if (!$recordDB) {
                //         throw new CustomException("Invalid Record!", 404);
                //     }
                   
                // }

                // if record have id, update record
                // if record have not id, generate id and create
                $Model::updateOrCreate(["id" => $record['id']], $record);
                $recordId = $record['id'];

                // to create translate record
                // if config no modelTranslate , not create record,
                if(isset($config['modelTranslate']) && isset($langsData)) {
                    $index = 0;
                   
                    //TODO: check lang_code if the store has the code
                    //if the store doesn't have lang_code, do nothing
                    
                    $classTranslate = $config['modelTranslate'];
                    $ModelTranslate = new $classTranslate;
                    foreach ($langsData as $keyLangcode =>$recordTranslate ) {
                        $index++; // avoid duplicate id

                        $recordTranslate['lang_code'] = $keyLangcode;
                        $recordTranslate[$config['parent_id']] = $record['id'];
                        $recordTranslate['id'] = isset($recordTranslate['id'])? $recordTranslate['id']: self::generateId($config['prefixLangId'].$index);

                        $ModelTranslate::updateOrCreate(["id" => $recordTranslate['id']], $recordTranslate);

                        $resObj['langs'][$keyLangcode] =  $recordTranslate;
                    }
                }

                DB::commit();

                //get record back
                $resObj = $record;//$Model::query()->findOrFail($recordId);
            } catch(QueryException $e){ 
                DB::rollback();
                throw new CustomException( "error message" , "error code", []);
            }catch (Exception $e) {
                DB::rollback();
                throw new CustomException( "error message" , "error code", []);
            }
        }
        
        return $resObj;
    }

    /**
     * Method to auto populate system fields
     */
    public static function generateSysFields(&$record, $isUpdate=false, $isAnonymous=false){

            $record['updated_by_id'] = ($isAnonymous ? "Anonymous" : Auth::user()->id);
            if (!$isUpdate) {
                $record['created_by_id'] = ($isAnonymous ? "Anonymous" : Auth::user()->id);
            }

        //system fields that we don't allow user to manually input
        unset($record['updated_date']);
        unset($record['created_date']);
    }

    
    public static function generateId($preFixId) {
     //   list($usec, $sec) = explode(" ", microtime());
        //$milliseconds = round(((float)$usec + (float)$sec) * 1000);

     //   $milliseconds = round(microtime(true) * 1000);
      //  return $preFixId.$milliseconds;
      return uniqid($preFixId);
    }
}
