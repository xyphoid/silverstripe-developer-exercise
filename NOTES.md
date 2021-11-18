# Development notes

Stopping after 2 hrs to package this up - there are obviously a few things to handle still: 

* themes/simple is code from SS's installer, ignored by default, i removed it from gitignore - the only actual new files are under themes/simple/templates/Blog
* I considered both parsing the files live on request or loading them into the Silverstripe database - ended up going down the latter path because it's more Silverstripe-representative, makes template handling a lot less fiddly and because it makes the API part much simpler.
* should the url slug be the Slug field in the files, or the filename itself as specced including .md? Presumed the former. 
* I'm not using tags.php, ran out of time to write code in there - the stopword list is copied into Post.php for now
* Some tags bugs - I need to think about more word characters, and the markdown -> html library I used throws exceptions on telling-the-story-through-graphic-design - charset issue? 
* Ran out of time for the API call - I intended to use https://github.com/silverstripe/silverstripe-restfulserver but that may not be the right format
* Data importer task has a hardcoded path and deletes all previous data when running - these are both not great (the second one means anyone hitting the site while we're importing might see missing content for example even if we are happy reloading it all)

# How to run this 

* checkout the site files
* $ composer install
* create a mysql database and user
* populate .env from .env.example
* set up your webserver with the docroot pointing at /public. I've tested with apache, IIRC nginx relies on more configuration as it can't just use .htaccess
* browse to site, allow Silverstripe to install
* log in to admin using /admin 
* browse to /dev/tasks and run the LoadBlogPostsTask - this should load up the Posts db table
* browse to /posts/