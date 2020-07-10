<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    protected $table = 'posts';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['langs'];
    protected $withCount = [];
    public $timestamps = true;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    public static $statusVal = ['draft' => 'draft', 'published' => 'published'];
    protected $fillable = [
        "id", 
        "created_date", 
        "created_by_id", 
        "updated_date", 
        "updated_by_id", 
        "is_backup", 
        "is_draft", 
        "author_id", 
        "posted_at", 
        "code", 
        "main_photo", 
        "status", //draft, publised
        "slug", 
        "category_ids", 
        "tag_ids", 
        "media_link", //it is video link
        "is_top", 
        "is_new",
        "ordering",
        "posts_id",
        "is_video",
        "record_type",
        "is_active",
        "is_external_link",
        "expiry_date"
    ];

    protected $casts = [
		"id" => "string",
		'author_id' => 'string',
		"is_backup" => "integer",
        "is_draft" => "integer",
        "is_top" => "integer", 
        "is_new" => "integer",
        "ordering" => "integer",
        "is_video" => "integer",
        "is_active" => "integer",
        "is_external_link" => "integer"
    ];

    protected $appends = ['main_photo_preview', "categories"];

    public function getMainPhotoPreviewAttribute(){
        return "{$this->main_photo}";
    }

    public function author(){
        return $this->belongsTo('App\Model\User', 'author_id');
    }

    public function langs(){
        return $this->hasMany('App\Model\PostTranslation', 'posts_id', 'id');
    }

    public function postCategories(){
        return $this->hasMany('App\Model\PostCategory', 'posts_id', 'id');
    }

    //to set format field
    public function setAuthorIdAttribute($value){
        $this->attributes['author_id'] = Auth::user()->id;
    }

    public function photos(){
        return $this->hasMany('App\Model\Photos', 'parent_id', "id");
    }

    public function subPosts(){
        return $this->hasMany('App\Model\Post', 'posts_id', 'id');
    }

    public function documents(){
        return $this->hasMany('App\Model\Documents', 'parent_id', 'id');
    }

    public function createdBy(){
        return $this->belongsTo('App\Model\User', 'created_by_id');
    }

    public function updatedBy(){
        return $this->belongsTo('App\Model\User', 'updated_by_id');
    }
    public function posts(){
        return $this->belongsTo('App\Model\Post', 'posts_id');
    }

    public function getCategoriesAttribute(){
        $categoryIds = $this->category_ids; 
        $lstCategoryIds = explode(",", $categoryIds);
        return Categories::whereIn("id", $lstCategoryIds)->get(); 
    }
}
