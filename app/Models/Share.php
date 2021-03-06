<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'post_id', 'shared_to', 'platform'];

    public function users()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function posts()
    {
        return $this->hasOne('App\Models\Post', 'id', 'post_id');
    }
}
