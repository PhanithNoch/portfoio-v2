<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PersonAccount extends Model
{
    protected $table = 'person_account';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs'];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
		"id", 
		"created_date", 
		"created_by_id", 
		"updated_date", 
		"updated_by_id", 
		"phone", 
		"email", 
		"commune", 
		"district", 
		"city", 
		"postalcode", 
		"country", 
		"billing_commune", 
		"billing_district", 
		"billing_city", 
		"billing_postalcode", 
		"billing_country", 
		"person_type", 
		"is_backup", 
		"person_code", 
		"users_id", 
		"photo", 
		"is_active", 
		"birthday", 
		"gender", 
		"date_hired", 
		"date_leave", 
		"facebook", 
		"twitter", 
		"pinterest", 
		"google_plus", 
		"mobile", 
		"other_phone", 
		"linkedin", 
		"title", 
		"marital_status", 
		"nationality", 
		"ethnicity", 
		"religion", 
		"birth_commune", 
		"birth_district", 
		"birth_city", 
		"birth_country", 
		"status", 
		"home_phone",
		"height", 
		"house_num", 
		"couple_divided", 
		"count_son", 
		"count_daughter", 
		"birth_house_num", 
		"is_alive", 
		"birth_village", 
		"village", 
		"fax", 
		"website",
		"ordering",
		"show_on_footer",
		"record_type",
		"row_ordering"
	];
	
    protected $casts = [
		"id" => "string",
		'phone' => 'string',
		"is_backup" => "integer",
		"is_active" => "integer",
		"mobile" => 'string',
		"other_phone" => 'string',
		"is_alive" => "integer",
		"count_son" => "integer",
		"count_daughter" => "integer",
		"ordering" => "integer",
		"show_on_footer" => "integer",
		"row_ordering" => "integer"
	];

	protected $appends = ['photo_preview'];

	public function getPhotoPreviewAttribute(){
        return "{$this->photo}";
	}
	
    public function langs(){
        return $this->hasMany('App\Model\PersonAccountTranslation', 'person_account_id', 'id');
    }
}
