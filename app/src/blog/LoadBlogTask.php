<?php

namespace Xyphoid\DeveloperExercise\Blog;

use SilverStripe\ORM\DB;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Control\Director;
use SilverStripe\ORM\Queries\SQLDelete;

class LoadBlogPostsTask extends BuildTask
{
    protected $title = 'LoadBlogPostsTask';

    protected $description = 'Parse blog posts from /assets folder into DB';

    protected $enabled = true; 

    public function run($request)
    {
        // This could be done in a few different ways - we could parse this live on request, but 
        // let's do it using Silverstripe data objects on the basis that we could then
        // extend this to add an admin UI for managing these posts. 

        // First delete any prev - this is obviously not something for production    
        $query = SQLDelete::create()
            ->setFrom('"Post"');            
        $query->execute();

        $assetfolder = Director::baseFolder().'/public/assets/posts';

        $md_posts = scandir($assetfolder);

        // scandir gives us . and .., we don't want those. 
        $md_posts = array_filter($md_posts, 
            function($e) 
            {
                return $e != '..' && $e != '.'; 
            }
        );

        foreach($md_posts as $md_post) {
            $full_path = $assetfolder."/".$md_post;
            $post = new Post();
            $post->loadFrom($full_path);
            $post->write();                        
        }    

        echo "Loaded ".count($md_posts)." posts from ".$assetfolder.".\n";
    }
}
