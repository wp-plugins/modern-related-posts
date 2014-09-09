<?php
/*
Plugin Name: Modern Related Posts
Plugin URI: http://www.wphigh.com/portfolio/modern-related-posts
Description: A beautiful, modern, animated, responsive Related Posts Plugin.
Version: 1.0.1
Author: wphigh
Author URI: http://www.wphigh.com
License: GPLv2 or later
Text Domain: mhpmrp
*/

class WHP_Modern_Related_Posts_Init {
	
	/**
	 * Current class instance.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance
	 */
	private static $instance;

	/**
	 * An instance of WHP_Modern_Related_Posts_Frontend().
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $related_posts
	 */	
	private $related_posts;
	
	/**
	 * Prevent to instance directly.
	 *
	 * @since 1.0.0
	 * @private
	 */
	private function __construct() {}

	/**
	 * Prevent to clone.
	 *
	 * @since 1.0.0
	 * @access private
	 */	
	private function __clone() {}
	
	/**
	 * Get instance only once.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return object	Current class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	
	/**
	 * Run
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function run() {
		// Hooks
		add_action( 'plugins_loaded', 										array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ) , 	array( $this, 'plugin_action_links_' ) );
		add_filter( 'plugin_row_meta', 										array( $this, 'plugin_meta_link' ), 10, 2 );
		
		add_action( 'wp',													array( $this, 'get_related_posts' ) );
		add_filter( 'the_content',											array( $this, 'render_related_posts' ), 999 );
		add_action( 'wp_enqueue_scripts',									array( $this, 'enqueue_scripts' ), 100 );
		add_action( 'wp_head',												array( $this, 'print_head_scripts' ), 100 );
		add_action( 'wp_footer',											array( $this, 'print_footer_scripts' ), 100 );
		
		// Admin settings
		require_once plugin_dir_path( __FILE__ ) . '/class-settings.php';
		new WHP_Modern_Related_Posts_Settings();
	}
	
	/**
	 * Load plugin textdomain for transration
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'mhpmrp', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
	
	/**
	 * Plugin action links
	 *
	 * Add plugin settings link
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $actions
	 * @return array
	 */
	public function plugin_action_links_( $actions ) {
		$actions['settings'] = sprintf( '<a href="%1$s">%2$s</a>', 
			add_query_arg( 'page', 'whp_modern_related_posts', admin_url( 'admin.php' ) ),
			__( 'Settings', 'mhpmrp' )
		);
		
		return $actions;		
	}
	
	/**
	 * Plugin row meta links
	 *
	 * Add plugin donate link
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array  $plugin_meta An array of the plugin's metadata,
	 *                            including the version, author,
	 *                            author URI, and plugin URI.
	 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
	 * @return array	 
	 */
	function plugin_meta_link( $plugin_meta, $plugin_file ) {
		if( plugin_basename( __FILE__ ) == $plugin_file ) {
			$plugin_meta['donate'] = '<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JM4997SM3VK8Y">' . __( 'Buy Me A Beer', 'mhpmrp' ) . '</a>';
		}
		
		return $plugin_meta;
	}
	
	/**
	 * Check current page can be show related posts or not
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return boolean
	 */
	private function is_related_posts_page() {
		$post_types = array( 'post' );
		if ( is_singular( $post_types ) )
			return true;
		
		return false;		
	}
	
	/**
	 * Get related posts instance of WHP_Modern_Related_Posts_Frontend
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_related_posts() {
		if ( ! $this->is_related_posts_page() )
			return;
		
		require_once plugin_dir_path( __FILE__ ) . '/class-frontend.php';
		$this->related_posts = new WHP_Modern_Related_Posts_Frontend();
	}
	
	/**
	 * Determine to show related posts page or not
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return boolean
	 */
	private function show_related_posts() {	
		if ( $this->is_related_posts_page() && $this->related_posts->have_posts() )
			return true;
		
		return false;
	}
	
	/**
	 * Generate related posts elements
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $content
	 * @return string
	 */
	public function render_related_posts( $content ) {		
		if ( $this->show_related_posts() ) {
			return $content . $this->related_posts->render();
		}
		
		return $content;
	}	
	
	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_scripts() {
		if ( ! $this->show_related_posts() )
			return;
		
		$this->related_posts->enqueue_scripts();
	}
	
	/**
	 * Print header scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */	
	public function print_head_scripts() {
		if ( ! $this->show_related_posts() )
			return;
					
		$this->related_posts->print_head_scripts();
	}		
	
	/**
	 * Print footer scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */	
	public function print_footer_scripts() {
		if ( ! $this->show_related_posts() )
			return;
					
		$this->related_posts->print_footer_scripts();
	}		
	
}


// Run
WHP_Modern_Related_Posts_Init::get_instance()->run();