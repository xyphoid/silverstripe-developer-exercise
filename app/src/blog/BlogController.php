<?php 
namespace Xyphoid\DeveloperExercise\Blog;

use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\Control\Director;
use SilverStripe\CMS\Controllers\ContentController;

class BlogController extends ContentController
{
    private static $allowed_actions = ['index','post'];
    private static $url_handlers = [        
        '$@' => 'index',              
    ];

    public function index($request)
    {
        if($request->remaining()) {
            return $this->showPost($request->remaining());
        } else {
            return $this->showPostList();
        }        
    }

    public function showPostList() 
    {
        $posts = Post::get();    

        return $this->customise([
            'Layout' => $this
                        ->customise(['Posts' => $posts])
                        ->renderWith('Blog\PostList')
        ])->renderWith(['Page']);
    }

    public function showPost($slug) 
    {
        $post = Post::get()->filter([
            'Slug' => $slug
        ])->first();

        if(!$post) {
            return $this->httpError(404, 'Unknown slug');
        }

        return $this->customise([
            'Layout' => $this
                        ->customise(['Post' => $post])
                        ->renderWith('Blog\Post')
        ])->renderWith(['Page']);


    }

}