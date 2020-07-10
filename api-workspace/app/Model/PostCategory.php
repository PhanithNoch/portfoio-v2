<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    protected $table = 'post_category';
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
		"posts_id",
		"categories_id"
    ];

    protected $casts = [
		"id" => "string",
		'posts_id' => 'string',
		"is_backup" => "integer",
		"categories_id" => "string",
    ];

    public function category(){
        return $this->belongsTo('App\Model\Categories', 'categories_id');
    }

    public function post(){
        return $this->belongsTo('App\Model\Post', 'posts_id');
    }
}
