<?php

namespace App\Repositories;

use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;

class PostRepository implements PostRepositoryInterface
{
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function store($data)
    {
        $post = $this->post->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => (auth()->user()->inRole('admin')) ? 'approved' : 'approval-pending',
            'created_at' => now(),
            'posted_by' => auth()->user()->id,
        ]);

        return $post;
    }

    public function update($data)
    {
        $post = $this->post->where('id', '=', $data['id'])->first();

        $post->title = $data['title'];
        $post->description = $data['description'];
        $post->save();

        return $post;
    }

    public function delete($id)
    {
        $post = $this->post->where('id', '=', $id)->delete();
        return $post; 
    }

    public function getAllPosts($data, $user)
    {
        if ($user->inRole('admin')) {

            $posts = $this->post->with('comments')->select("posts.*","users.name")->join('users','posts.posted_by','=','users.id');

            if (isset($data['search'])) {
                $posts->where('title', 'like', '%' . $data['search'] . '%');
            }

            if (isset($data['filter']) && $data['filter']!='0') {
                $posts->where('status', '=', $data['filter']);
            }

            $posts = $posts->get();

        } else {
            
            $posts = $this->post->with('comments')->select("posts.*","users.name")->join('users','posts.posted_by','=','users.id');

            if (isset($data['search'])) {
                $posts->where('title', 'like', '%' . $data['search'] . '%');
            }

            $posts->where(function($q) use ($user){
                $q->where('posted_by', '=', $user->id)
                ->orWhere('status','=','approved');
            });

            $posts = $posts->get();

        }

        return $posts;
    }

    public function getPostById($id)
    {
        $post = $this->post->with('comments')
            ->leftJoin('users', 'users.id', 'posts.posted_by')
            ->where('posts.id', '=', $id)
            ->select('posts.*', 'users.name')
            ->first();

        return $post;
    }

    public function approveOrReject($data)
    {
        $post = $this->post->where('id', '=', $data['id'])->first();
        $post->status = $data['status'];
        $post->save();

        return $post;
    }

 
   

}
