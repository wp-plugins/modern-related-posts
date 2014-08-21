<?php
/**
 * Admin settings
 *
 * @since 1.0.0
 */

class WHP_Modern_Related_Posts_Settings {
	
	/**
	 * Set option group
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $option_group
	 */
	private $option_group = 'whp_modern_related_posts_group';
	
	/**
	 * Set option name
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $option_name
	 */	
	private $option_name = 'whp_modern_related_posts';
	
	/**
	 * Set option values
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array $option_vals
	 */	
	private $option_vals;

	/**
	 * Set admin page hook suffix
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $hook_suffix
	 */	
	private $hook_suffix;

	/**
	 * Set sections page slug
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $sections_page
	 */	
	private $sections_page = 'whp_modern_related_posts_sections';
	
	/**
	 * Constructor
	 *
	 * Add hooks
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_menu',									array( $this, 'add_options_menu' ) );
		add_action( 'admin_init',									array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts',						array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_print_styles',							array( $this, 'admin_print_styles' ) );
		add_action( 'admin_print_footer_scripts',					array( $this, 'admin_print_footer_scripts' ) );
	}
	
	/**
	 * Add sub menu page to the settings menu
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function add_options_menu() {
		$this->hook_suffix = add_options_page( 'Modern Related Posts', __( 'Modern Related Posts', 'mhpmrp' ), 'manage_options', 'whp_modern_related_posts', array( $this, 'settings_page' ) );
	}
	
	/**
	 * The content of related posts settings page
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function settings_page() {
?>
<div id="modern-related-posts" class="wrap">
	<h2><?php _e( 'Modern Related Posts', 'mhpmrp' )?></h2>
	<form method="post" action="options.php">
		<?php 
			$this->option_vals = get_option( $this->option_name );
			settings_fields( $this->option_group );
			do_settings_sections( $this->sections_page );
			submit_button(); 
		?>
	</form>
</div>
<?php
	$this->sidebar();	
	}
	
	/**
	 * Display elements at the right sidebar
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function sidebar() {
?>
<div id="mrp-sidebar">
	<aside class="item donate">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="JM4997SM3VK8Y">
		<input type="image" src="<?php echo plugins_url( 'assets/donate.gif', __FILE__ ); ?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="<?php echo plugins_url( 'assets/donate.gif', __FILE__ ); ?>" width="1" height="1">
		</form>
	</aside>
	<aside class="item themeforest">
		<a href="http://themeforest.net/popular_item/by_category?category=wordpress&ref=wphigh" target="_blank"><img src="<?php echo plugins_url( 'assets/themeforest.jpg', __FILE__ ); ?>"></a>
	</aside>
	<aside class="item codecanyon">
		<a href="http://codecanyon.net/popular_item/by_category?category=wordpress&ref=wphigh" target="_blank"><img src="<?php echo plugins_url( 'assets/codecanyon.jpg', __FILE__ ); ?>"></a>
	</aside>
</div>
<?php	
	}
	
	/**
	 * Register settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( $this->option_group, $this->option_name, array( $this, 'sanitize_cb' ) );
		
		// Sections
		add_settings_section( 'mhpmrp_general', __( 'General', 'mhpmrp' ), array( $this, 'general_section' ), $this->sections_page );
		add_settings_section( 'mhpmrp_appearance', __( 'Appearance', 'mhpmrp' ), array( $this, 'appearance_section' ), $this->sections_page );
		add_settings_section( 'mhpmrp_debug', __( 'Debug', 'mhpmrp' ), array( $this, 'debug_section' ), $this->sections_page );
		
		// Fields for general section
		add_settings_field( 'posts_num', __( 'Posts Number', 'mhpmrp' ), array( $this, 'field_posts_num' ), $this->sections_page, 'mhpmrp_general', array( 'label_for' => $this->get_field_id( 'posts_num' ) ) );
		add_settings_field( 'orderby', __( 'Orderby', 'mhpmrp' ), array( $this, 'field_orderby' ), $this->sections_page, 'mhpmrp_general', array( 'label_for' => $this->get_field_id( 'orderby' ) ) );
		add_settings_field( 'related_via', __( 'Related Via', 'mhpmrp' ), array( $this, 'field_related_via' ), $this->sections_page, 'mhpmrp_general', array( 'label_for' => $this->get_field_id( 'related_via' ) ) );
		
		// Fields for appearance section
		add_settings_field( 'margin_top', __( 'Margin Top', 'mhpmrp' ), array( $this, 'field_margin_top' ), $this->sections_page, 'mhpmrp_appearance', array( 'label_for' => $this->get_field_id( 'margin_top' ) ) );
		add_settings_field( 'margin_bottom', __( 'Margin Bottom', 'mhpmrp' ), array( $this, 'field_margin_bottom' ), $this->sections_page, 'mhpmrp_appearance', array( 'label_for' => $this->get_field_id( 'margin_bottom' ) ) );
		add_settings_field( 'title', __( 'Title', 'mhpmrp' ), array( $this, 'field_title' ), $this->sections_page, 'mhpmrp_appearance', array( 'label_for' => $this->get_field_id( 'title' ) ) );
		add_settings_field( 'title_size', __( 'Title Size', 'mhpmrp' ), array( $this, 'field_title_size' ), $this->sections_page, 'mhpmrp_appearance', array( 'label_for' => $this->get_field_id( 'title_size' ) ) );
		add_settings_field( 'display_num', __( 'Number of visible', 'mhpmrp' ), array( $this, 'field_display_num' ), $this->sections_page, 'mhpmrp_appearance', array( 'label_for' => $this->get_field_id( 'display_num' ) ) );
		add_settings_field( 'aspect_ratio', __( 'Aspect Ratio', 'mhpmrp' ), array( $this, 'field_aspect_ratio' ), $this->sections_page, 'mhpmrp_appearance', array( 'label_for' => $this->get_field_id( 'aspect_ratio' ) ) );
		add_settings_field( 'item_bg_color', __( 'Background Color', 'mhpmrp' ), array( $this, 'field_item_bg_color' ), $this->sections_page, 'mhpmrp_appearance', array( 'label_for' => $this->get_field_id( 'item_bg_color' ) ) );
		add_settings_field( 'overlay_bg_color', __( 'Hover Background Color', 'mhpmrp' ), array( $this, 'field_overlay_bg_color' ), $this->sections_page, 'mhpmrp_appearance', array( 'label_for' => $this->get_field_id( 'overlay_bg_color' ) ) );
		add_settings_field( 'link_size', __( 'Link Size', 'mhpmrp' ), array( $this, 'field_link_size' ), $this->sections_page, 'mhpmrp_appearance', array( 'label_for' => $this->get_field_id( 'link_size' ) ) );
		add_settings_field( 'nav_bg_color', __( 'Navigation Background Color', 'mhpmrp' ), array( $this, 'field_nav_bg_color' ), $this->sections_page, 'mhpmrp_appearance', array( 'label_for' => $this->get_field_id( 'nav_bg_color' ) ) );
		add_settings_field( 'auto_play', __( 'Auto Play', 'mhpmrp' ), array( $this, 'field_auto_play' ), $this->sections_page, 'mhpmrp_appearance', array( 'label_for' => $this->get_field_id( 'auto_play' ) ) );		
		
		// Fields for debug section
		add_settings_field( 'content_width', __( 'Content Width', 'mhpmrp' ), array( $this, 'field_content_width' ), $this->sections_page, 'mhpmrp_debug', array( 'label_for' => $this->get_field_id( 'content_width' ) ) );
	}
	
	/**
	 * Sanitize option data
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data
	 * @return array
	 */
	public function sanitize_cb( $data ) {
		$data['posts_name'] = absint( $data['posts_name'] );
		$data['margin_top'] = intval( $data['margin_top'] );
		$data['margin_bottom'] = intval( $data['margin_bottom'] );
		$data['title'] = sanitize_text_field( $data['title'] );
		$data['title_size'] = absint( $data['title_size'] );
		$data['display_num'] = absint( $data['display_num'] );
		$data['aspect_ratio'] = sanitize_text_field( $data['aspect_ratio'] );
		$data['item_bg_color'] = call_user_func( array( $this, 'sanitize_hex_color' ), $data['item_bg_color'] );
		$data['overlay_bg_color'] = call_user_func( array( $this, 'sanitize_hex_color' ), $data['overlay_bg_color'] );
		$data['link_size'] = absint( $data['link_size'] );
		$data['nav_bg_color'] = call_user_func( array( $this, 'sanitize_hex_color' ), $data['nav_bg_color'] );
		$data['content_width'] = sanitize_text_field( $data['content_width'] );
		$data['auto_play'] = absint( $data['auto_play'] );
		
		return $data;
	}
	
	/**
	 * Sanitize hex color
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function sanitize_hex_color( $color ) {
		if ( '' === $color )
			return '';
	
		// 3 or 6 hex digits, or the empty string.
		if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
			return $color;
	
		return null;
	}	
	
	/**
	 * General section content
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function general_section() {	
	}

	/**
	 * Appearance section content
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function appearance_section() {	
	}
	
	/**
	 * Debug section content
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function debug_section() {	
	}	
	
	/**
	 * Get field name
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $name
	 * @return string
	 */	
	private function get_field_name( $name ) {
		return "{$this->option_name}[{$name}]";
	}
	
	/**
	 * Get field value
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	private function get_field_val( $name , $default = '' ) {
		return isset( $this->option_vals[ $name ] ) ? $this->option_vals[ $name ] : $default;
	}
	
	/**
	 * Get field id
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $name
	 * @return string
	 */
	private function get_field_id( $name ) {
		return "mhpmrp_$name";
	}
	
	/**
	 * Display field description to the field bottom
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $content
	 * @return void
	 */
	private function field_des( $content ) {
		echo '<p class="description">' .$content . '</p>';
	}
	
	/**
	 * Posts number field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_posts_num() {
		$name = 'posts_num';
		printf( '<input type="number" id="%1$s" class="small-text" name="%2$s" value="%3$s">',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name, 10 ) )
		);
		
		$this->field_des( __( 'Enter the number of related posts.', 'mhpmrp' ) );
	}
	
	/**
	 * Posts orderby field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_orderby() {
		$name = 'orderby';
		printf( '<select id="%1$s" name="%2$s"><option value="date"%3$s>%4$s</option><option value="rand"%5$s>%6$s</option></select>', 
			$this->get_field_id( $name ),
			$this->get_field_name( $name ),
			selected( 'date', $this->get_field_val( $name, 'rand' ), false ),
			__( 'Date', 'mhpmrp' ),
			selected( 'rand', $this->get_field_val( $name, 'rand' ), false ),
			__( 'Random', 'mhpmrp' )			
		);
	}
	
	/**
	 * Posts related via field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_related_via() {
		$name = 'related_via';
		printf( '<select id="%1$s" name="%2$s"><option value="category"%3$s>%4$s</option><option value="tag"%5$s>%6$s</option></select>', 
			$this->get_field_id( $name ),
			$this->get_field_name( $name ),
			selected( 'category', $this->get_field_val( $name, 'tag' ), false ),
			__( 'Categories', 'mhpmrp' ),
			selected( 'tag', $this->get_field_val( $name, 'tag' ), false ),
			__( 'Tags', 'mhpmrp' )			
		);
		
		$this->field_des( __( "If you select categories, then by current post's categories to find related posts.", 'mhpmrp' ) );
	}
	
	/**
	 * Margin top field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_margin_top() {
		$name = 'margin_top';
		printf( '<input type="number" id="%1$s" class="small-text" name="%2$s" value="%3$s"> px',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name, 20 ) )
		);
	}
	
	/**
	 * Margin bottom field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_margin_bottom() {
		$name = 'margin_bottom';
		printf( '<input type="number" id="%1$s" class="small-text" name="%2$s" value="%3$s"> px',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name, 20 ) )
		);
	}		
	
	/**
	 * Title field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_title() {
		$name = 'title';
		printf( '<input type="text" id="%1$s" class="regular-text" name="%2$s" value="%3$s">',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name, 'Related Posts' ) )
		);
	}
	
	/**
	 * Title size field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_title_size() {
		$name = 'title_size';
		printf( '<input type="number" id="%1$s" class="small-text" name="%2$s" value="%3$s"> px',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name, 20 ) )
		);
	}
	
	/**
	 * The number of visible
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_display_num() {
		$name = 'display_num';
		printf( '<input type="number" id="%1$s" class="small-text" name="%2$s" value="%3$s">',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name, 4 ) )
		);
		
		$this->field_des( __( 'Set the maximum amount of items displayed at a time with the widest browser width.', 'mhpmrp' ) );
	}
	
	/**
	 * Aspect ratio of the item
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_aspect_ratio() {
		$name = 'aspect_ratio';
		printf( '<input type="text" id="%1$s" class="small-text" name="%2$s" value="%3$s">',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name, '4:3' ) )
		);
		
		$this->field_des( __( 'e.g. 4:3', 'mhpmrp' ) );
	}
	
	/**
	 * The background color of item
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_item_bg_color() {
		$name = 'item_bg_color';
		printf( '<input type="text" id="%1$s" class="mrp-color-picker" name="%2$s" value="%3$s" data-default-color="#000">',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name, '#000' ) )
		);
		
		$this->field_des( __( 'Displayed when the post has not attached pictures.', 'mhpmrp' ) );
	}
	
	/**
	 * The background color of hovering the item
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_overlay_bg_color() {
		$name = 'overlay_bg_color';
		printf( '<input type="text" id="%1$s" class="mrp-color-picker" name="%2$s" value="%3$s" data-default-color="#dd3333">',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name, '#dd3333' ) )
		);
		
		$this->field_des( __( 'the background color when the mouse hover.', 'mhpmrp' ) );
	}
	
	/**
	 * Link size field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_link_size() {
		$name = 'link_size';
		printf( '<input type="number" id="%1$s" class="small-text" name="%2$s" value="%3$s"> px',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name, 16 ) )
		);
	}
	
	/**
	 * The background color of navigation
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_nav_bg_color() {
		$name = 'nav_bg_color';
		printf( '<input type="text" id="%1$s" class="mrp-color-picker" name="%2$s" value="%3$s" data-default-color="#000">',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name, '#000' ) )
		);
	}
	
	/**
	 * Auto play field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_auto_play() {
		$name = 'auto_play';
		printf( '<input type="number" id="%1$s" class="small-text" name="%2$s" value="%3$s"> ms',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			$this->get_field_val( $name, 5000 )
		);
		
		$this->field_des( 'e.g. 5000: play every 5 seconds. 0: disabled auto play.', 'mhpmrp' );
	}	
	
	/**
	 * Content width field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function field_content_width() {
		$name = 'content_width';
		printf( '<input type="number" id="%1$s" class="small-text" name="%2$s" value="%3$s"> px',
			esc_attr( $this->get_field_id( $name ) ),
			esc_attr( $this->get_field_name( $name ) ),
			esc_attr( $this->get_field_val( $name ) )
		);
		
		$this->field_des( __( 'Maybe some themes has not set the variable $content_width or incorrect value. Enter the correct value to fix it if the slider layout displayed incorrectly.<br>See <a href="http://codex.wordpress.org/Content_Width" target="_blank">http://codex.wordpress.org/Content_Width</a>', 'mhpmrp' ) );
	}
	
	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $hook
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( $this->hook_suffix != $hook ) 
			return;
		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}
	
	/**
	 * print admin header styles
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function admin_print_styles() {
		global $hook_suffix;
		if ( $this->hook_suffix != $hook_suffix ) 
			return;
?>
<style type="text/css">
	#modern-related-posts { float: left; max-width: 70%; }
	#mrp-sidebar { float: right; margin-top: 45px; margin-right: 10px;}
	#mrp-sidebar .item { margin-bottom: 20px; }
	#mrp-sidebar .donate form { text-align: center; }
	#mrp-sidebar .themeforest img, #mrp-sidebar .codecanyon img { width: 200px; height: auto; }
</style>
<?php
	}			
	
	/**
	 * print admin footer scripts
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */	
	public function admin_print_footer_scripts() {
		global $hook_suffix;
		if ( $this->hook_suffix != $hook_suffix ) 
			return;
?>
<script type="text/javascript">
jQuery(function($) {
	$( '#modern-related-posts .mrp-color-picker' ).wpColorPicker();
});
</script>
<?php
	}
	
}


/**
 * Get option of related posts
 *
 * @since 1.0.0
 *
 * @param string $name
 * @param mixed $default
 *
 * @return mixed
 */
function mhpmrp_get_option( $name, $default = '' ) {
	global $Mhp_Modern_Related_Posts_options;
	if ( ! isset( $Mhp_Modern_Related_Posts_options ) ) {
		$Mhp_Modern_Related_Posts_options = get_option( 'whp_modern_related_posts' );
	}
	
	$val = isset( $Mhp_Modern_Related_Posts_options[ $name ] ) ? $Mhp_Modern_Related_Posts_options[ $name ] : $default;
	
	return apply_filters( 'mhpmrp_get_option', $val, $name, $default );
}