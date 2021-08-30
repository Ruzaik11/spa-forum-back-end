<?php

namespace App\Repositories;

use DB;
use Carbon\Carbon;
use App\Models\Comment;
use App\Interfaces\CommentRepositoryInterface;

class CommentRepository implements CommentRepositoryInterface
{
    private $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }



    public function store($data,$user)
    {
        $comment =  $this->comment->create([
                        'post_id' => $data['post_id'],
                        'comment_by' => $user->id,
                        'comment' => $data['comment'],
                        'created_at' => now(),
                    ]);

        return $comment;
    }

    public function getComments($data)
    {
        $comments = $this->comment->where('post_id', '=', $data['id'])->get();

        return $comments;
    }








}
