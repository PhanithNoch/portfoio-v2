<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Photos extends Model
{
    protected $table = 'photos';
    protected $keyType = 'string';
    public $incrementing = false; 
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
        "is_backup", 
        "parent_id", 
        "ordering", 
        "thumbnail", 
        "photo"
    ]; 

    protected $casts = [
		"id" => "string",
		'parent_id' => 'string',
		"is_backup" => "integer",
        "ordering" => "integer"
    ];

    protected $appends = ['photo_preview', 'thumbnail_preview'];

    public function getPhotoPreviewAttribute(){
        return "{$this->photo}";
    }

    public function getThumbnailPreviewAttribute(){
        return "{$this->thumbnail}";
    }
}
