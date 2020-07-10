<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs'];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id",
		"title",
		"phone",
		"email",
		"username",
		"district",
		"commune",
		"country",
		"city",
		"password",
		"api_token",
		"photo",
		"is_active",
		"is_locked",
		"created_date",
		"created_by_id",
		"updated_date",
		"updated_by_id",
		"currency_code",
		"user_type",
		"is_admin",
		"status",
		"user_roles_id",
		"is_backup",
		"user_code",
		"company_id",
		"lock_reason",
		"user_permission_id",
		"record_type",
		"profile_ids"
    ];
	
	protected $appends = ['photo_preview'];

	public function getPhotoPreviewAttribute(){
        return "{$this->photo}";
	}
	
	protected $hidden = [
        'password'
	];
	
    public function company(){
        return $this->belongsTo('App\Model\Company', 'company_id');
    }
	
	public function userPermission(){
		return $this->belongsTo('App\Model\Permissions', 'user_roles_id');
	}
	
    public function langs(){
        return $this->hasMany('App\Model\UserTranslation', 'users_id', 'id');
    }

	

    public function loginHistories(){
        return $this->hasMany('App\Model\LoginHistory', 'users_id', 'id');
	}
	
	//to set value or convert value for create/update
    public function setPasswordAttribute($value){
        $this->attributes['password'] = bcrypt($value);
    }
}
