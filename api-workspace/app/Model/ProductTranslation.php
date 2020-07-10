<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    protected $table = 'product_translation';
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
        "lang_code", 
        "products_id", 
        "name", 
        "short_desc", 
        "description", 
        "notes", 
        "other_note", 
        "short_spec"
    ];

    protected $casts = [
		"id" => "string", 
		"is_backup" => "integer", 
    ];
}
