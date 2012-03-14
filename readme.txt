=== External Related Posts ===
Contributors: mintindeed
Tags: pingback, trackback, automated links, seo, post, related posts
Requires at least: 3.2
Tested up to: 3.2
Stable tag: 1.2.1

Grabs related links from Google Blog Search, inserts a link to them into your post and gives them a pingback.

== Description ==

** This plugin now works! ** It broke shortly after I originally uploaded it last year.  People are still downloading it, so I decided to make it work.  I revamped it and made it totally compatible with the latest version of WordPress.

External Related Posts takes the category, tags or custom taxonomy from your post (or custom post type) and searches Google Blog Search for related posts.  External Related Posts then adds a link to that blog to your post and pings them, letting them know you made a relevant post on their subject.

Controls exist for how many blogs to search for, whether to only post links to blogs that accept pingbacks, how many pingbacks to perform, and a randomizer for how often External Related Posts activates (e.g., every post, 50% of your posts, 25% of your posts).

Github: https://github.com/mintindeed/external-related-posts

WordPress.org: http://wordpress.org/extend/plugins/external-related-posts/

Image by [Toban Black](http://www.flickr.com/photos/tobanblack/3773116901/); some rights reserved.  See link for details.

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.  Alternately, use automatic installation from WordPress's admin screens.

== Changelog ==
= 1.2.1 =
* Fixed incorrect screenshot.  No plugin functionality was changed.

= 1.2 =
* Updated Google Blog Search URL
* Made sure it works with WordPress 3.2
* Support for custom taxonomies
* Support for custom post types
* No longer requires its own table, now stores its cache in the WordPress options table

= 1.1 =
* Final update
* No changes
* No longer supporting nor updating this plugin

= 1.0 =
* Initial release
* Branched from PingCrawl

== Frequently Asked Questions ==

= The links I'm getting suck, they're barely relevant at all. =

External Related Posts uses either your post's tags, categories, or title to search for related blogs.  Try changing your tags, categories or title -- this will also change the results you get.


= External Related Posts isn't working! My posts don't have any links. =

Not every post will return results.  There are a few potential reasons for this:

*   Chance of running
    External Related Posts contains a setting that determines how frequently it runs.  If "Chance of running" is not 100%, then not every post will activate External Related Posts.
*   Use tags, categories, or a custom taxonomy
    External Related Posts uses either your post's tags, categories, or custom taxonomy to search for related blogs.  If you're not getting many results, try changing which one you use.
*   Search criteria
    Try using different tags/categories/taxonomy term.  For example, if External Related Posts is searching by tags, try changing which tags you're using.  Do your own manual Google Blog Search with your terms and see what results come up.
*   Verify blog is accepting pingbacks
    Not every blog accepts pingbacks.  You may not get pingbacks, but you will get links.
*   Wait a few days and try again
    Google Blog Search is always indexing new posts.  If you're not getting any results, go back and re-publish your post at a later date and hope that someone has a post that matches your criteria.
*   Number of results
    In External Related Posts settings, increase the number of results to get from blog search.  Note that this will increase the time it takes to publish your post.

= Why does it take so long to post articles now? =

External Related Posts needs to fetch and process Google search results in order to do its job.

= Why do I get a "Page not found" error when posting? =

The script is either running out of memory or executing for too long.

This can be remedied in two ways:

* Increase PHP's `max_execution_time` and/or `memory_limit` in your php.ini, or
* Reduce the "Number of links to make"

= How can I prevent External Related Posts from running on a given post? =

Set "Chance of Running" to 0% until you want to use it again.

= How can I re-ping a given post? =

Delete the previously added links (including the title added by External Related Posts) from the post.

= Why don't my pingbacks show up at the sites I'm pinging? =

* Blog owners may delete your pingbacks.
* Blog owners may mark your pingbacks as spam.
* If you've been spammy with pingbacks in the past, your new pingbacks might be marked as spam by anti-ping-spam plugins.

== Licence ==

External Related Posts is released under GPL v2.

== Screenshots ==
1. External Related Posts options page.
2. How External Related Posts might look on your blog.

