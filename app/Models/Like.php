<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id', 'post_id'];
    protected $table ='likes';
    use HasFactory;
    public function users()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function posts()
    {
        return $this->hasOne('App\Models\Post', 'id', 'post_id');
    }
}
