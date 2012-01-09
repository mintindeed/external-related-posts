<?php
/**
 * Handles the generation of the External Related Posts
 * @since 1.2
 * @version 1.2
 */
class ExternalRelatedPosts {

	/**
	 * Hook into actions and filters here, along with any other global setup
	 * that needs to run when this plugin is invoked
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function __construct() {
		add_action( 'transition_post_status', array(&$this, 'get_related_posts'), 10, 3 );
	}

	/**
	 * Get the related posts when status transitions to "publish"
	 *
	 * @since 1.2
	 * @version 1.2
	 */
    public function get_related_posts( $new_status, $old_status, $post ) {

		// Don't do anything for non-published posts, or already-published posts
		if ( 'publish' !== $new_status ) {
			return;
		}

        if ( strpos($post->post_content, ExternalRelatedPosts_Options::get_option('related_links_title')) !== false )
			return;

        if ( rand(0, 99) > ExternalRelatedPosts_Options::get_option('chance_to_run') )
			return;

		$search_terms = $this->get_post_keywords($post->ID);

		// Start our $data string with the external links title
		$erp_title = ExternalRelatedPosts_Options::get_option('related_links_title');

		// Check if the original post already has external links by searching for our title string.
		// @todo Admin option for: upon update replace previous related posts with new ones

		if( strpos($post->post_content, $erp_title) !== false )
			return;

		$rss_items = $this->get_items( $search_terms );

		if ( is_null($rss_items) ) {
			return;
		}

		$post->post_content .= $erp_title;
		$post->post_content .= '<ul class="external-related-links">';
		$post->post_content .= $rss_items;
		$post->post_content .= '</ul>';

		wp_update_post($post);
	}

	/**
	 * Get keywords from the selected taxonomy
	 *
	 * @since 1.2
	 * @version 1.2
	 *
	 * @param int $post_id
	 *
	 * @return null|array
	 */
	public function get_post_keywords($post_id) {
		$taxonomy = ExternalRelatedPosts_Options::get_option('use_taxonomy');

		$post_terms = wp_get_object_terms((int)$post_id, $taxonomy, $args = array('fields' => 'names'));

		if ( !empty($post_terms) ) {
			// Make the post term values unique
			$post_terms = array_flip(array_flip($post_terms));
			return $post_terms;
		}

		return null;
	}

	/**
	 * Fetch the items from Google Blog Search
	 *
	 * @since 1.2
	 * @version 1.2
	 *
	 * @param array $search_terms
	 *
	 * @return string $cache_items|$cache['rss'] HTML string of results
	 */
    public function get_items( $search_terms = array() ) {
        if ( empty($search_terms) )
            return null;

		$key = 'erp_' . md5(implode(' ', $search_terms));
		$cache = get_option($key);

        // @todo Option to set the cache ttl.  It's 1 day right now.
        $today = date("Y-m-d", strtotime("-1 day"));
		if ( $cache && $cache['updated'] == $today ) {
			return $cache['rss'];
		}

        // @todo Search services other than Google Blog Search
        // @todo Set language
		$search_url = 'http://www.google.com/search?hl=en&tbm=blg&output=rss&q=' . urlencode(implode(' ', $search_terms));

		$date_range = ExternalRelatedPosts_Options::get_option('date_range');
		if ( $date_range ) {
			$search_url .= '&tbs=qdr:' . $date_range;
		}

        // Filter out various sites based on the url.
        // @todo Make this list configurable.
        /*
        // Filter out .htm(l)
        $aUri .= urlencode(' -inurl:.htm');
        // Filter out ning.com
        $aUri .= urlencode(' -inurl:ning.com');
        // Filter out .asp(x)
        $aUri .= urlencode(' -inurl:.asp');
        // Filter out blogspot
        $aUri .= urlencode(' -inurl:blogspot.com');
        // Filter out LiveJournal
        $aUri .= urlencode(' -inurl:livejournal.com');
        // Filter out vBulletin
        $aUri .= urlencode(' -inurl:vbulletin');
        */

        $rss = fetch_feed($search_url);
        if ( is_wp_error($rss) ) {
            return null;
        }

		// Figure out how many total items there are, but limit it user set value
		$linkcount = ExternalRelatedPosts_Options::get_option('linkcount');
		$maxitems = $rss->get_item_quantity($linkcount);

		// Build an array of all the items, starting with element 0 (first element).
		$rss_items = $rss->get_items(0, $maxitems);

		if ($maxitems == 0) {
			return null;
		}

		// @todo User option for format
		// @see http://simplepie.org/wiki/reference/start#simplepie_item
		$cache_items = '';
		foreach ( $rss_items as $item ) {
			$cache_items .= '<li><a href="' . $item->get_permalink() . '">' . strip_tags($item->get_title()) . '</a></li>';
		}

		// Cache the results
		$cache = array(
			'rss' => $cache_items,
			'updated' => $today,
		);
		update_option($key, $cache);

        return $cache_items;
    }
}

//EOF - End of File, do not remove, do not put anything after this line