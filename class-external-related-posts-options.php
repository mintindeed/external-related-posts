<?php
/**
 * Handles get/set of plugin options and WordPress options page
 *
 * @since 1.2
 * @version 1.2
 */
class ExternalRelatedPosts_Options
{

	/**
	 * String to use for the plugin name.  Used for generating class names, etc.
	 *
	 * @var string
	 */
	public static $plugin_id = 'external-related-posts';

	/**
	 * String to use for the textdomain filename
	 *
	 * @var string
	 */
	public static $text_domain = 'external-related-posts';

	/**
	 * Name of the option group for WordPress settings API
	 *
	 * @var string
	 */
	protected $_option_group = 'external-related-posts-group';

	/**
	 * Name of the option for WordPress settings API
	 *
	 * @var string
	 */
	public static $option_name = 'external_related_posts_options';

	/**
	 * Contains default optionss that get overridden in the constructor
	 *
	 * @var array
	 */
	public static $options_defaults = array(
		'feed_table' => 'erp_feed_cache',
		'use_taxonomy' => 'post_tag',
		'chance_to_run' => 100,
		'date_range' => 'd',
		'linkcount' => 5,
		'related_links_title' => '<h4>Related External Links</h4>',
		'update_titles' => 1,
		'post_types' => array(
			'post' => 'post',
		),
	);

	/**
	 * Contains merged defaults + saved options
	 *
	 * @var array
	 */
	public static $options = array();

	/**
	 * Taxonomies that are useless for generating links
	 *
	 * @var array
	 */
	protected $_taxonomy_blacklist = array(
		'post_format',
	);

	/**
	 * Post types that are useless for generating links
	 *
	 * @var array
	 */
	protected $_post_type_blacklist = array();

	/**
	 * Hook into actions and filters here, along with any other global setup
	 * that needs to run when this plugin is invoked
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function __construct() {
		add_action( 'admin_menu', array(&$this,'admin_menu'), 10, 0 );
		add_action( 'admin_init', array(&$this,'register_settings'), 99, 0 );
	}

    /**
     * Returns Singleton instance of this plugin
     *
	 * @since 1.2
	 * @version 1.2
	 *
     * @return ExternalRelatedPosts_Options
     */
    public static function instance()
    {
        static $_instance = null;

        if ( is_null($_instance) ) {
            $class = __CLASS__;
            $_instance = new $class();
        }

        return $_instance;
    }

	/**
     * Merge the saved options with the defaults
     *
	 * @since 1.2
	 * @version 1.2
     */
    public static function setup_options() {
		self::$options = array_merge(self::$options_defaults, get_option( self::$option_name, array() ));
    }

	/**
	 * Add the admin menu page
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function admin_menu() {
		add_options_page( __('External Related Posts Configuration', self::$text_domain), __('External Related Posts', self::$text_domain), 'manage_options', self::$plugin_id, array(&$this, 'settings_page') );
	}

	/**
	 * Register the plugin settings with the WordPress settings API
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function register_settings() {
		register_setting( $this->_option_group, self::$option_name, array(&$this, 'settings_section_validate_main') );

		add_settings_section(self::$plugin_id . '-main', 'Main Settings', array(&$this, 'settings_section_description_main'), self::$plugin_id);

		add_settings_field($this->_option_group . '-related_links_title', 'Related links title:', array(&$this, 'settings_field_related_links_title'), self::$plugin_id, self::$plugin_id . '-main');

		add_settings_field($this->_option_group . '-update_titles', '', array(&$this, 'settings_field_update_titles'), self::$plugin_id, self::$plugin_id . '-main');

		add_settings_field($this->_option_group . '-post_types', 'Post types to include', array(&$this, 'settings_field_post_types'), self::$plugin_id, self::$plugin_id . '-main');

		add_settings_field($this->_option_group . '-use_taxonomy', 'Use:', array(&$this, 'settings_field_use_taxonomy'), self::$plugin_id, self::$plugin_id . '-main');

		add_settings_field($this->_option_group . '-chance_to_run', 'Chance of Running:', array(&$this, 'settings_field_chance_to_run'), self::$plugin_id, self::$plugin_id . '-main');

		add_settings_field($this->_option_group . '-date_range', 'Limit by date:', array(&$this, 'settings_field_date_range'), self::$plugin_id, self::$plugin_id . '-main');

		add_settings_field($this->_option_group . '-linkcount', 'Number of links to make:', array(&$this, 'settings_field_linkcount'), self::$plugin_id, self::$plugin_id . '-main');

		add_settings_section(self::$plugin_id . '-help', 'Help', array(&$this, 'settings_section_description_help'), self::$plugin_id);
	}

	/**
	 * Output the description HTML for the "Main" settings section
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function settings_section_description_main() {
	}

	/**
	 * Help text
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function settings_section_description_help() {
		?>
		<p>Not every post will return results, so even with External Related Posts activating on every post you may have posts with fewer links and pingbacks than you have set.</p>
		<p>There may be no seach results that match your settings, the only way around this is to change your External Related Posts settings to use different search criteria.</p>
		<p>For example:</p>
		<ul>
			<li>If using "tags" then use "category" instead</li>
			<li>Go back and re-publish your post at a later date and hope that someone has a post that matches your search</li>
		</ul>
		<?php
	}

	/**
	 * Related Links title setting
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function settings_field_related_links_title() {
		$settings_field_name = 'related_links_title';
		$related_links_title = self::get_option($settings_field_name);
		$id = $this->_option_group . '-' . $settings_field_name;

		// Make sure the field is wide enough
		$field_length = (strlen($sitemap_filename) > 40) ? strlen($sitemap_filename)+5 : 40;

		echo '<input type="hidden" name="' . self::$option_name . '[related_links_title_current]" value="' . $related_links_title . '" />';
		echo '<input name="' . self::$option_name . '[' . $settings_field_name . ']" id="' . $id . '" type="text" size="' . $field_length . '" value="' . $related_links_title . '" />';
		echo '<br /><i>Example: &lt;h4&gt;Related External Links&lt;h4&gt;</i>';
	}

	/**
	 * Update titles setting
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function settings_field_update_titles() {
		$settings_field_name = 'update_titles';
		$update_titles = self::get_option($settings_field_name);

		$id = $this->_option_group . '-' . $settings_field_name;

		echo '<input type="hidden" name="' . self::$option_name . '[' . $settings_field_name . ']" value="0" />';
		echo '<label><input name="' . self::$option_name . '[' . $settings_field_name . ']" id="' . $id . '" type="checkbox" value="1" ' . checked($update_titles, '1', false) . '/>&nbsp;Update existing posts with the new title</label><br />';
	}

	/**
	 * Post type setting
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function settings_field_post_types() {
		$settings_field_name = 'post_types';
		$post_types = self::get_option($settings_field_name);
		$wp_post_types = get_post_types($args = array('public' => true), $output = 'names');
		foreach ( $wp_post_types as $post_type ) {
			// Don't output anything for any blacklisted taxonomies
			if ( array_search($post_type, $this->_post_type_blacklist) !== false ) {
				continue;
			}

			$id = $this->_option_group . '-' . $settings_field_name . '-' . $post_type;
			echo '<label><input name="' . self::$option_name . '[' . $settings_field_name . '][' . $post_type . ']" id="' . $id . '" type="checkbox" value="' . $post_type . '" ' . checked(isset($post_types[$post_type]), true, false) . '/>&nbsp;' . ucwords(str_replace('_', ' ', $post_type)) . '</label><br />';
		}
	}

	/**
	 * Use taxonomy setting
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function settings_field_use_taxonomy() {
		$settings_field_name = 'use_taxonomy';
		$use_taxonomy = self::get_option($settings_field_name);
		$taxonomies = get_taxonomies($args = array('public' => true), $output = 'names');
		foreach ( $taxonomies as $taxonomy ) {
			// Don't output anything for any blacklisted taxonomies
			if ( array_search($taxonomy, $this->_taxonomy_blacklist) !== false ) {
				continue;
			}

			$id = $this->_option_group . '-' . $settings_field_name . '-' . $taxonomy;
			echo '<label><input name="' . self::$option_name . '[' . $settings_field_name . ']" id="' . $id . '" type="radio" value="' . $taxonomy . '" ' . checked($taxonomy, $use_taxonomy, false) . '/>&nbsp;' . ucwords(str_replace('_', ' ', $taxonomy)) . '</label><br />';
		}
	}

	/**
	 * Chance to run setting
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function settings_field_chance_to_run() {
		$settings_field_name = 'chance_to_run';
		$chance_to_run = self::get_option($settings_field_name);
		$chances = array(
			0 => 0,
			25 => 25,
			50 => 50,
			75 => 75,
			100 => 100,
		);
		foreach ( $chances as $chance ) {
			$id = $this->_option_group . '-' . $settings_field_name . '-' . $chance;
			echo '<label><input name="' . self::$option_name . '[' . $settings_field_name . ']" id="' . $id . '" type="radio" value="' . $chance . '" ' . checked($chance, $chance_to_run, false) . '/>&nbsp;' . $chance . '%</label><br />';
		}
	}

	/**
	 * Date range setting
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function settings_field_date_range() {
		$settings_field_name = 'date_range';
		$date_range = self::get_option($settings_field_name);
		$ranges = array(
			'0' => 'Any time',
			'n10' => 'Past 10 minutes',
			'h' => 'Past hour',
			'd' => 'Past 24 hours',
			'w' => 'Past week',
			'm' => 'Past month',
			'y' => 'Past year',
		);
		foreach ( $ranges as $range_value => $range ) {
			$id = $this->_option_group . '-' . $settings_field_name . '-' . $range_value;
			echo '<label><input name="' . self::$option_name . '[' . $settings_field_name . ']" id="' . $id . '" type="radio" value="' . $range_value . '" ' . checked($range_value, $date_range, false) . '/>&nbsp;' . $range . '</label><br />';
		}
	}

	/**
	 * Link count setting
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function settings_field_linkcount() {
		$settings_field_name = 'linkcount';
		$linkcount = self::get_option($settings_field_name);
		$id = $this->_option_group . '-' . $settings_field_name;

		// Make sure the field is wide enough
		$field_length = strlen($linkcount)+2;

		echo '<input name="' . self::$option_name . '[' . $settings_field_name . ']" id="' . $id . '" type="text" size="' . $field_length . '" value="' . $linkcount . '" />';
		echo '<br />Must be between 1-10.';
	}

	/**
	 * Validate the options being saved in the "Main" settings section
	 *
	 * @since 1.2
	 * @version 1.2
	 *
	 * @todo Typecase keys/values, including recursive arrays
	 * @todo There's gotta be a better way to validate the options...
	 */
	public function settings_section_validate_main($options) {
        global $wpdb;

        $related_links_title = trim($options['related_links_title']);

        $related_links_title_current = $options['related_links_title_current'];
        unset($options['related_links_title_current']);

        if( 1 == $options['update_titles'] && $related_links_title != $related_links_title_current ) {
            $sql = "UPDATE " . $wpdb->posts . " SET post_content=REPLACE(post_content,'" . $wpdb->escape($related_links_title_current) . "','" . $wpdb->escape($related_links_title) . "') WHERE post_type IN ('" . implode("','", $options['post_types']) . "') AND post_status='publish'";

            $wpdb->query( $sql );
        }

		// Link count must be between 1-10
		if ( $options['linkcount'] < 1 ) {
			$options['linkcount'] = 1;
		} elseif ( $options['linkcount'] > 10 ) {
			$options['linkcount'] = 10;
		}

		foreach ( $options as $key => $value ) {
			if ( is_string($value) ) {
				self::$options[$key] = trim($value);
			} else {
				self::$options[$key] = $value;
			}
		}

		return $options;
	}

	/**
	 * Output the settings page
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public function settings_page() {
		?>
		<div class="wrap">
			<h2><?php _e('External Related Posts', self::$text_domain); ?></h2>
			<form method="post" action="options.php">
				<?php
				settings_fields( $this->_option_group );
				do_settings_sections( self::$plugin_id );
				?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes', self::$text_domain) ?>" />
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Plugin option getter
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public static function get_option($option_key = '') {
		if ( empty(self::$options) ) {
			self::setup_options();
		}

		if ( isset(self::$options[$option_key]) ) {
			return self::$options[$option_key];
		}

		return null;
	}

	/**
	 * Plugin option setter
	 *
	 * @since 1.2
	 * @version 1.2
	 */
	public static function set_option($option_key, $option_value = '') {
		self::$options[$option_key] = $option_value;
	}


}