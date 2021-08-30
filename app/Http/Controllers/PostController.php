<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Validators\PostApiValidator;
use App\Http\Controllers\BaseApiController;
use App\Interfaces\PostRepositoryInterface;
use App\Interfaces\CommentRepositoryInterface;

class PostController extends BaseApiController
{
    //
    private $postRepository;
    private $commentRepository;

    public function __construct(PostRepositoryInterface $postRepository , CommentRepositoryInterface $commentRepository)
    {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
    }

    public function store(Request $request)
    {

        try {

            $data = $request->except(array_keys($request->query()));

            $validateRequest = PostApiValidator::storePost($data);

            if (!$validateRequest->fails()) {

                $post = $this->postRepository->store($data);

                return response()->json([
                    'error' => false,
                    'data' => $post,
                ], 200);

            } else {
                return response()->json([
                    'error' => true,
                    'message' => $validateRequest->errors()->all(),
                ], 400);
            }

        } catch (\Throwable $th) {

            return $this->returnErrorMessage($th);

        }
    }

    public function update(Request $request)
    {

        try {

            $data = $request->except(array_keys($request->query()));

            $validateRequest = PostApiValidator::updatePost($data);

            if (!$validateRequest->fails()) {

                if (auth()->user()->inRole('admin')) {

                    $post = Post::where('id', '=', $data['id'])->first();

                } else {

                    $post = Post::where('id', '=', $data['id'])->where('posted_by', '=', auth()->user()->id)->first();

                }

                if (!$post) {

                    return response()->json([
                        'error' => true,
                        'message' => 'Post not found',
                    ], 404);
                }

                $post = $this->postRepository->update($data);

                return response()->json([
                    'error' => false,
                    'data' => $post,
                ], 200);

            } else {
                return response()->json([
                    'error' => true,
                    'message' => $validateRequest->errors()->all(),
                ], 400);
            }

        } catch (\Throwable $th) {

            return $this->returnErrorMessage($th);

        }
    }

    public function delete(Request $request)
    {

        try {

            if (auth()->user()->inRole('admin')) {

                $post = Post::where('id', '=', $request->id)->first();
                
                Comment::where('post_id', '=', $request->id)->delete();
                

            } else {
                $post = Post::where('id', '=', $request->id)->where('posted_by', '=', auth()->user()->id)->first();
            }

            if (!$post) {

                return response()->json([
                    'error' => true,
                    'message' => 'Post not found',
                ], 404);
            }

            $post = $this->postRepository->delete($post->id);

            return response()->json([
                'error' => false,
                'msg' => 'Post deleted successfully',
            ], 200);



        } catch (\Throwable $th) {
            return $this->returnErrorMessage($th);
        }
    }

    public function getAllPosts(Request $request)
    {
  
        try {

            $data = $request->all();
          
            $posts = $this->postRepository->getAllPosts($data,auth()->user());

            return response()->json([
                'error' => false,
                'data' => $posts,
            ], 200);


        } catch (\Throwable $th) {
            return $this->returnErrorMessage($th);
        }
    }

    public function getPostById(Request $request, $id)
    {

        try {

            $post = $this->postRepository->getPostById($id);

            if (!$post) {
                return response()->json([
                    'error' => true,
                    'message' => 'Post Not Found',
                ], 404);
            }

            return response()->json([
                'error' => false,
                'data' => $post,
            ], 200);

        } catch (\Throwable $th) {
            return $this->returnErrorMessage($th);
        }
    }



    
    public function approveOrReject(Request $request)
    {

        try {

            if (!auth()->user()->inRole('admin')) {
                
                return response()->json([
                    'error' => true,
                    'message' => 'Access Denied',
                ], 401);
            }

            $data = $request->except(array_keys($request->query()));

            $validateRequest = PostApiValidator::approveOrReject($data);

            if (!$validateRequest->fails()) {

                $post = Post::find($request->id);

                if (!$post) {

                    return response()->json([
                        'error' => true,
                        'message' => 'Post not found',
                    ], 404);

                }

                $posts = $this->postRepository->approveOrReject($request->toArray());


                return response()->json([
                    'error' => false,
                    'data' => $post,
                ], 200);

            } else {
                return response()->json([
                    'error' => true,
                    'message' => $validateRequest->errors()->first(),
                ], 400);
            }

        } catch (\Throwable $th) {

            return $this->returnErrorMessage($th);

        }
    }


    public function postComment(Request $request)
    {

        try {

            $data = $request->except(array_keys($request->query()));

            $validateRequest = PostApiValidator::postComment($data);

            if (!$validateRequest->fails()) {

                $post = Post::where('id', '=', $data['id'])->first();

                if (!$post) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Post not found',
                    ], 404);
                }

                $data['post_id'] = $post->id;

                $comment = $this->commentRepository->store($data,auth()->user());

                return response()->json([
                    'error' => false,
                    'data' => $comment,
                ], 200);

            } else {
                return response()->json([
                    'error' => true,
                    'message' => $validateRequest->errors()->first(),
                ], 400);
            }

        } catch (\Throwable $th) {
          return $this->returnErrorMessage($th);
        }
    }

    public function getComments(Request $request)
    {

        try {

            $data = $request->all();

            $validateRequest = PostApiValidator::getComments($data);

            if (!$validateRequest->fails()) {

                $post = Post::where('id', '=', $data['id'])->first();

                if (!$post) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Post not found',
                    ], 404);
                }

                $comments = $this->commentRepository->getComments($data);

                return response()->json([
                    'error' => true,
                    'data' => $comments,
                ], 200);

            } else {
                return response()->json([
                    'error' => true,
                    'message' => $validateRequest->errors()->first(),
                ], 400);
            }

        } catch (\Throwable $th) {

            return $this->returnErrorMessage($th);

        }
    }

}
