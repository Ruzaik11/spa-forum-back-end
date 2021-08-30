<?php

namespace App\Providers;

use App\Repositories\PostRepository;
use App\Repositories\CommentRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\PostRepositoryInterface;
use App\Interfaces\CommentRepositoryInterface;


class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
    }
}
