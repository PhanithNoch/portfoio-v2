<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductPhoto extends Model
{
    protected $table = 'product_photo';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable = [
        "id",
		"created_date",
		"created_by_id",
		"updated_date",
		"updated_by_id",
		"photo_link",
		"thumbnail_link",
		"products_id",
		"is_backup"
    ];

    protected $casts = [
		"id" => "string", 
		"is_backup" => "integer", 
    ];


    public function product(){
        return $this->belongsTo('App\Model\Products', 'products_id');
    }
}
