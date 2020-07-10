<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';
    
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    protected $with = ["langs"];//The relations to eager load on every query.
    protected $withCount = [];//The relationship counts that should be eager loaded on every query.

    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    protected $fillable = [
        "id",
        "district",
        "commune",
        "website",
        "domain",
        "facebook",
        "g_plus",
        "created_date",
        "updated_date",
        "email",
        "instagram",
        "pinterest",
        "twitter",
        "dribble",
        "city",
        "country",
        "phone",
        "mobile",
        "created_by_id",
        "updated_by_id",
        "is_active",
        "logo_link",
        "store_code",
        "is_backup",
        "aes_key",
        "other_phone",
        "postcode",
        "billing_country",
        "billing_city",
        "billing_commune",
        "billing_district",
        "billing_postcode",
        "company_type",
        "lat",
        "log",
        "light_logo",
        "dark_logo",
        "favorite_ico",
        "youtube",
        "linkedin",
        "main_branch_id",
        "map_embed_link"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_active" => "integer",
        "phone" => "string",
        "mobile" => "string",
        "other_phone" => "string"
    ];

    protected $appends = ['light_logo_preview', 'dark_logo_preview', 'favorite_ico_preview'];
    
    public function getLightLogoPreviewAttribute(){
        return $this->light_logo;
    }
    public function getDarkLogoPreviewAttribute(){
        return $this->dark_logo;
    }
    public function getFavoriteIcoPreviewAttribute(){
        return $this->favorite_ico;
    }
    
    /** relation table */ 
    
    /** child (relationship one to many)*/
    public function langs(){
        return $this->hasMany('App\Model\CompanyTranslation', 'company_id', 'id');
    }

    public function users(){
        return $this->hasMany('App\Model\User', 'company_id', 'id');
    }

    public function availableLanguages(){
        return $this->hasMany('App\Model\AvailableLanguages', 'company_id', 'id');
    }

    public function branches(){
        return $this->hasMany('App\Model\Company', 'main_branch_id', 'id');
    }

    public function companyContacts(){
        return $this->hasMany('App\Model\CompanyContact', 'company_id', 'id');
    }
 
}
