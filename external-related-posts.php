<?php
/*
Plugin Name: External Related Posts
Plugin URI: http://gabrielkoen.com/wordpress-plugins/external-related-posts/
Description: Grabs related links from Google Blog Search, inserts a link to them into your post and gives them a pingback.
Version: 1.2.1
Author: Gabriel Koen
Author URI: http://gabrielkoen.com/
License: GPL2
*/

/*  Copyright 2011  Gabriel Koen  (email : gabriel.koen@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Plugin is not needed when doing ajax calls
if ( !defined('DOING_AJAX') || !DOING_AJAX ) {
	// Plugin directory needs to be the same as the plugin filename
	$plugin_path = dirname( __FILE__ );

	include_once $plugin_path . '/class-external-related-posts-options.php';
	add_action('plugins_loaded', 'external_related_posts_options_loader');

	include_once $plugin_path . '/class-external-related-posts.php';
	add_action('plugins_loaded', 'external_related_posts_loader');
}

/**
 * For loading ExternalRelatedPosts via WordPress action
 *
 * @since 1.2
 * @version 1.2
 */
function external_related_posts_loader() {
	new ExternalRelatedPosts();
}

/**
 * For loading ExternalRelatedPosts_Options via WordPress action
 *
 * @since 1.2
 * @version 1.2
 */
function external_related_posts_options_loader() {
	ExternalRelatedPosts_Options::instance();
}

//EOF