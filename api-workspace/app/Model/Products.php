<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\ProductCategories;
use Illuminate\Support\Collection;

class Products extends Model
{
    protected $table = 'products';
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
        "photo", 
        "is_backup", 
        "is_active", 
        "type", 
        "is_new", 
        "has_stock", 
        "best_sell", 
        "slug", 
        "tag_ids", 
        "frequency_ids", 
        "is_comming", 
        "category_ids",
        "status",
        "video_link",
        "has_video",
        "is_recommend",
        "record_type_id",
        "product_code",
        "sku",
        "valuation_method",
        "default_category_id",
        "for_post_sale",
        "for_sale",
        "for_expense"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer", 
        "is_active" => "integer",
        "is_new" => "integer",
        "has_stock" => "integer",
        "best_sell" => "integer",
        "is_comming" => "integer",
        "has_video" => "integer"
    ];

    protected $appends = ['photo_preview'];

    public function getPhotoPreviewAttribute(){
        return "{$this->photo}";
    } 

    public function langs(){
        return $this->hasMany('App\Model\ProductTranslation', 'products_id', 'id');
    }

    public function photos(){
        return $this->hasMany('App\Model\ProductPhoto', 'products_id', 'id');
    }

    public function pricebookEntries(){
        return $this->hasMany('App\Model\PricebookEntry', 'products_id', 'id')->with("pricebook");
    }
}
