<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories';
    
    protected $keyType = 'string';//set value type if primary key isn't int
    public $incrementing = false;//set this to false if your primary key isn't auto increase

    protected $with = ["langs"];//The relations to eager load on every query.
    protected $withCount = [];//The relationship counts that should be eager loaded on every query.

    public $timestamps = true;//set to false if you wish not create created_at and updated_at in DB
    const CREATED_AT = 'created_date'; //change here if you wish to change column created_at name
    const UPDATED_AT = 'updated_date'; //change here if you wish to change column updated_at name

    protected $fillable = [
        "id",
        "ordering",
        "created_date",
        "updated_date",
        "created_by_id",
        "updated_by_id",
        "record_type",//it can be product, portfolio, article
        "parent_id",
        "is_backup",
        "image",
        "icon",
        "is_default",
        "slug",
        "not_delete",
        "code",
        "is_active"
    ];

    protected $casts = [
		"id" => "string", 
        "is_backup" => "integer",
        "is_default" => "integer"
    ];

    protected $appends = ['image_preview'];

    public function getImagePreviewAttribute(){
        return "{$this->image}";
    }

    // public function getTotalProductAttribute(){
    //     // $count = ProductCategories::where('categories_id', $this->id)
    //     //             ->count();
    //     $count = Products::where('category_ids', 'like', '%'. $this->id .'%')->count();
    //     return $count; 
    // }

    /** relation table */

    /** parent (Relationship many to one)*/
    public function parent(){
        return $this->belongsTo('App\Model\Categories', "parent_id");
    }
    
    /** child (relationship one to many)*/
    public function langs(){
        return $this->hasMany('App\Model\CategoryTranslation', 'categories_id', 'id');
    }

    public function subCategories(){
        return $this->hasMany('App\Model\Categories', 'parent_id', 'id')->with('subCategories')->orderBy("ordering", 'asc');
    }

    public function productCategories(){
        return $this->hasMany('App\Model\ProductCategories', 'categories_id', 'id')->where('category_type', 'product');
    }

    public function postCategories(){
        return $this->hasMany('App\Model\PostCategory', 'categories_id', 'id')->where('category_type', 'post');
    }

    
}
