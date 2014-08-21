<?php
/**
 * Display modern related posts in the frontend pages
 *
 * @since 1.0.0
 */

class WHP_Modern_Related_Posts_Frontend {
	
	/**
	 * An instance of WP_Query()
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var object $posts_query
	 */
	private $posts_query;
	
	/**
	 * An instance of frontend
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var object $frontend
	 */	
	private $frontend;
	
	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		require_once plugin_dir_path( __FILE__ ) . '/class-related-posts.php';
		$instance = new WHP_Modern_Related_Posts_Query();
		$this->posts_query = $instance->get_instance();
		
		$this->skins_factory();
	}	
	
	/**
	 * Get related posts skins
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function skins_factory() {
		require_once plugin_dir_path( __FILE__ ) . '/skins/owl-carousel/class-carousel.php';
		$this->frontend = new WHP_Modern_Related_Posts_Skins_Carousel( $this->posts_query );
	}	
	
	/**
	 * Check to have posts or not
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function have_posts() {
		return (boolean) $this->posts_query->found_posts;
	}
	
	/**
	 * Generate all elements to related posts
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function render() {
		if ( ! $this->posts_query->found_posts )
			return;
		
		$content = $this->frontend->render( $this->posts_query );
		
		// Restore original post datas
		wp_reset_postdata();
		
		return $this->wrap( $content );
	}
	
	/**
	 * Get heading tag to the title
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @string
	 */
	private function get_heading_tag() {
		return 'h3';
	}
	
	/**
	 * Get the title
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function get_title() {
		return sprintf( '<%1$s class="mrp-title">%2$s</%1$s>', 
			$this->get_heading_tag(),
			mhpmrp_get_option( 'title', 'Related Posts' )
		);
	}
	
	/**
	 * Wrap the related posts elements
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function wrap( $content ) {
		return sprintf( '<div id="%1$s" class="whp-modern-related-posts">%2$s%3$s</div>',
			esc_attr( 'whp-modern-related-posts-' . get_the_ID() ),
			$this->get_title(),
			$content
		);
	}
	
	/**
	 * Print header scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */		
	public function print_head_scripts() {
?>
<style type="text/css">
	.whp-modern-related-posts { margin-top: <?php echo mhpmrp_get_option( 'margin_top', 20 ); ?>px; margin-bottom: <?php echo mhpmrp_get_option( 'margin_bottom', 20 ); ?>px; }
	.whp-modern-related-posts .mrp-title { margin: 0 0 15px; padding: 0; font-size: <?php echo mhpmrp_get_option( 'title_size', 20 ); ?>px; }	
</style>
<?php
		if ( method_exists( $this->frontend, 'print_head_scripts' ) ) {
			$this->frontend->print_head_scripts();
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_scripts() {
		if ( method_exists( $this->frontend, 'enqueue_scripts' ) ) {
			$this->frontend->enqueue_scripts();
		}
	}

	/**
	 * Print footer scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */		
	public function print_footer_scripts() {
		if ( method_exists( $this->frontend, 'print_footer_scripts' ) ) {
			$this->frontend->print_footer_scripts();
		}
	}
	
}