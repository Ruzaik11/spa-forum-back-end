<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'post_date', 'posted_by'];

    public function getPostDateAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function comments()
    {
        return $this->hasMany('\App\Models\Comment', 'post_id', 'id')
            ->join('users', 'users.id', '=', 'comments.comment_by')
            ->select("users.name", 'comments.*')->orderBy('created_at','desc');
    }

}
