<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductCategories extends Model
{
    protected $table = 'product_categories';
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
		"is_backup",
		"products_id",
		"categories_id"
    ];

    protected $casts = [
		"id" => "string",
        'products_id' => 'string',
        'categories_id' => 'string',
		"is_backup" => "integer", 
    ];

    public function product(){
        return $this->belongsTo('App\Model\Products', 'products_id');
    }

    public function category(){
        return $this->belongsTo('App\Model\Categories', 'categories_id');
    }
}
