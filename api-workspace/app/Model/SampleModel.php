<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SampleModel extends Model
{
    protected $table = 'person_account';
   
    protected $primaryKey = 'id'; //table primary key column name, change it if it has different name
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    protected $with = ["langs"];//The relations to eager load on every query.
    protected $withCount = [];//The relationship counts that should be eager loaded on every query.

    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    protected $fillable = [];//allow which fill to add into DB

    /** relation table */

    /** parent (Relationship many to one)*/
    public function parent(){
        return $this->belongsTo('model path', "parent column name");
    }
    
    /** child (relationship one to many)*/
    public function langs(){
        return $this->hasMany('model path', 'foreing key column', 'primary key');
    }


    //formula fields
    protected $appends = [];//add custom formula field
    
    public function getCustomFieldAttribute(){
        return "value";
    }

    //to set value or convert value for create/update
    public function setFieldNameAttribute($value){
       $this->attributes['column_name'] = strtolower($value);
    }
}
