<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AppMenus extends Model
{
    protected $table = 'app_menus';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = [];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id", "created_date", "created_by_id", "updated_date", "updated_by_id", 
        "is_backup", "sys_id", "type", "icon", "parent_nav_id", "name", "app_id", "target", "ordering",
        "title","icon","path","class","is_external_link","is_divider","parent_menu_id",
        "application_id"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "sys_id" => "integer",
        "ordering" => "integer"
    ];

    public function subMenus(){
        return $this->hasMany('App\Model\AppMenus', "parent_nav_id", "id")->orderBy('ordering', 'asc');
    }
}
