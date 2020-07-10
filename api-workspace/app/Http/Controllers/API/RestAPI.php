<?php

namespace App\Http\Controllers\API;

use App\Services\DataConvertionClass;
use Validator;
use Illuminate\Http\Request;
use App\Exceptions\CustomException; 
use App\Http\Controllers\TriggerHandler;
use App\Http\Resources\RestResource;
use App\Services\DatabaseGW;
use App\Services\Helper;

abstract class RestAPI extends TriggerHandler
{
    protected abstract function getQuery();
    protected abstract function getModel();
    protected abstract function getTableSetting();

    /**
     * Method to check input value before insert/update
     * @param array $lstRecords list object records or object record as array
     * @param array $rules      array rules to check
     * @param array $customMsg  array custom message
     */
    public function validation($lstRecords, $rules=[], $customMsg=[]){
        if (empty($rules)) return;
 
        foreach ($lstRecords as $index => $record) {
            $validate = Validator::make($record, $rules, $customMsg);
            if ($validate->fails()) {
                throw new CustomException($validate->errors()->first(), 
                                            CustomException::$INVALID_FIELD, 
                                            $validate->errors()); 
            }
        }
    }
    
    /** Function that need to override in controller */

    /** 
     * Function to get validation rule before create
     * @return array list of rules
     */
    public function getCreateRules(){
        return [];
    }

    /**
     * Function to get validation rule before update
     * @return array list of rules
     */
    public function getUpdateRules(){
        return [
            "id" => "required"
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        try{
            $model = $this->getQuery();
            $lstRecords = DatabaseGW::queryByModel($model, $request->all());
            return RestResource::collection($lstRecords);
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $model = $this->getModel();
            $record = $model::findOrFail($id);

            //return single record as resource
            return new RestResource($record);
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    /**
     * Store many newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request 
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        try{ 
            $lstRequestData = $request->all();  
            $this->validation($lstRequestData, $this->getCreateRules());//validation before make action 
 
            //run logic before creation, the method is depend on each controller
            $this->beforeCreate($lstRequestData); 
            $lstRecordCreated = $this->upsert($lstRequestData);//login to create record goes here

            //run any logic after creation, the method is depend on each controller
            $this->afterCreate($lstRecordCreated);

            //get those record from database back because after upsert it is data from request
            $lstIds = [];
            foreach ($lstRecordCreated as $index => $record) {
                $lstIds[] = $record["id"];
            }
            if(!empty($lstIds)){
                $model = $this->getQuery();
                $lstRecords = DatabaseGW::queryByModel($model, ["id"=> implode(",", $lstIds), "limit" => (count($lstIds) + 1)]);
                return RestResource::collection($lstRecords);
            } 
            return $this->respondSuccess([]);
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $record = $request->all();
            $record["id"] = $id;
            $lstRequestData = [$record];
            $this->validation($lstRequestData, $this->getUpdateRules());

            $model = $this->getModel();
            $lstOldRecords = $model::where("id", $id)->get();

            //get old record to check up with any required logic
            //convert list old record into map key=id, value=record
            $collectionOldRecord = $lstOldRecords->mapWithKeys(function($item){
                return [$item['id'] => $item];
            });
            $mapOldRecords = $collectionOldRecord->all();

            //call trigger to fire
            $this->beforeUpdate($lstRequestData, $mapOldRecords);
            $lstRecordUpdated = $this->upsert($lstRequestData);
            $this->afterUpdate($lstRecordUpdated);

            //get those record from database back because after upsert it is data from request
            return $this->show($lstRecordUpdated[0]["id"]);
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    /**
     * Method to handle bulk update record
     */
    public function updates(Request $request){
        try{ 
            $lstRequestData = $request->all();
            $this->validation($lstRequestData, $this->getUpdateRules());

            //convert request data into map key=id value=record
            $collection = collect($lstRequestData);
            $collectionNewRecord = $collection->mapWithKeys(function ($item) {
                return [$item['id'] => $item];
            });
            
            //get all editing record ids
            $recordIds = $collectionNewRecord->keys()->all();

            //get old records to do other action in trigger
            $model = $this->getModel();
            $lstOldRecords = $model::whereIn("id", $recordIds)->get();

            //convert list old record into map key=id, value=record
            $collectionOldRecord = $lstOldRecords->mapWithKeys(function($item){
                return [$item['id'] => $item];
            });
            $mapOldRecords = $collectionOldRecord->all();
            
            //call trigger to fire
            $this->beforeUpdate($lstRequestData, $mapOldRecords);
            $lstRecordUpdated = $this->upsert($lstRequestData);
            $this->afterUpdate($lstRecordUpdated);

            //get those record from database back because after upsert it is data from request
            $lstIds = [];
            foreach ($lstRecordUpdated as $index => $record) {
                $lstIds[] = $record["id"];
            }
            if(!empty($lstIds)){
                $model = $this->getQuery();
                $lstRecords = DatabaseGW::queryByModel($model, ["id"=> implode(",", $lstIds), "limit" => (count($lstIds) + 1)]);
                return RestResource::collection($lstRecords);
            } 
            return $this->respondSuccess([]);
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    public function upsert($lstRecords){ 
        $lstRecordUpserted = [];
        $tableConfig = $this->getTableSetting();
        
        if(!empty($tableConfig) && !empty($lstRecords)){
            foreach ($lstRecords as $index => $record) { 
                $lstRecordUpserted[] = DatabaseGW::updateOrCreate($record, $tableConfig, $index, false);
            }
        }

        $lstFilterNull = array_filter($lstRecordUpserted, function($var){
            return isset($var) && !empty($var);
        });
        return $lstFilterNull;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $model = $this->getModel();
            $record = $model::findOrFail($id);

            if($record->delete()){
                return new RestResource($record);
            }
            return false;
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }

    public function destroys(Request $request){ 
        try{
            $ids = $request->all(); 
            if(empty($ids)) return $this->respondSuccess([]);
            
            $model = $this->getModel();
            $record = $model::whereIn("id", $ids);

            if($record->delete()){
                return $this->respondSuccess([]);
            }
            return $this->customException("Cannot Delete!");
        }catch(\Exception $ex){
            return $this->respondError($ex);
        }
    }
}
