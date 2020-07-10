<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Applications extends Model
{
    protected $table = 'applications';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id", "created_date", "created_by_id", "updated_date", "updated_by_id", 
        "is_backup", "sys_id", "name", "active", "icon", "is_default", "ordering",
        "company_id"

    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "sys_id" => "integer",
        "active" => "integer",
        "is_default" => "integer",
        "ordering" => "integer"
    ];

    public function appPermissions(){
        return $this->hasMany("App\Model\AppPermission", "app_id", "id");
    }

    public function menus(){
        return $this->hasMany("App\Model\AppMenus", "app_id", "id")
                    ->where("parent_nav_id", null)
                    ->with("subMenus")
                    ->orderBy('ordering', 'asc');
    }
}