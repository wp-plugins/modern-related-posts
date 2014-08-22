<?php
/**
 * Generate elements with carousel
 *
 * @since 1.0.0
 */
 
class WHP_Modern_Related_Posts_Skins_Carousel {
	
	/**
	 * Generate carousel elements
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object	an instance of new WHP_Modern_Related_Posts_Query()
	 * @return string
	 */
	public function render( $posts_query ) {
		$content = $this->content( $posts_query );
		return sprintf( '<div class="owl-carousel mrpc-carousel">%s</div>', $content );
	}
	
	/**
	 * Generate carousel content elements
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function content( $posts_query ) {
		$items = '';
		$size = $this->get_thumbnail_size();
		while ( $posts_query->have_posts() ) {
			$posts_query->the_post();
			$items .= $this->item( $size );
		}
		
		return $items;
	}	
	
	/**
	 * Generate item elements for carousel content
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function item( $size ) {
		return sprintf( '<div class="mrpc-item"><span class="mrpc-overlay"></span>%1$s<span class="mrpc-link"><a href="%2$s">%3$s</a></span></div>',
			$this->get_thumbnail( $size ),
			esc_url( get_permalink() ),
			$this->get_title()
		);
	}
	
	/**
	 * Get the post image
	 *
	 * Get the featured image at first, then check attached image
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 */		
	private function get_thumbnail( $size = 'thumbnail' ) {
		global $post;
		
		if( has_post_thumbnail() ) {
			return get_the_post_thumbnail( $post->ID, $size );
		}		
		
		$args = array( 
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'post_parent' => $post->ID,
			'order' => 'asc'
		);
		
		$images = get_children( $args );
		if( $images ) {
			$attachment_id = key( $images );
			return wp_get_attachment_image( $attachment_id, $size );
		}
		else {
			$preg = "/<img.*?>/i"; 
			$content = $post -> post_content;
			preg_match( $preg, $content, $img );
			if( isset( $img[0] ) && $img[0] )
				return $img[0];
		}
		
		return '';
	}
	
	/**
	 * Get thumbnail size
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function get_thumbnail_size() {
		$width = $this->get_item_width();
		
		$thumbnail_size_w = get_option( 'thumbnail_size_w' );
		if ( $width <= $thumbnail_size_w ) {
			return 'thumbnail';
		}
		
		$medium_size_w = get_option( 'medium_size_w' );
		if ( $width <= $medium_size_w ) {
			return 'medium';
		}
		
		$large_size_w = get_option( 'large_size_w' );
		if ( $width <= $large_size_w ) {
			return 'large';
		}
		
		return 'full';		
	}

	/**
	 * Get the post title
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 */	
	private function get_title() {
		$title = get_the_title();
		
		if ( empty( $title ) )
			return get_the_ID();
		
		return $title;
	}
	
	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'mhpmrp-owl-carousel', plugins_url( 'owl.carousel.css', __FILE__ ), '', '1.3.3' );
		wp_enqueue_script( 'mhpmrp-owl-carousel', plugins_url( 'owl.carousel.min.js', __FILE__ ), array( 'jquery' ), '1.3.3', true );
		
		$data = $this->inline_style();
		wp_add_inline_style( 'mhpmrp-owl-carousel', $data );
	}
	
	/**
	 * Get inline style
	 *
	 * Set background image if have featured image, otherwise se background color
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 */	
	private function inline_style() {
		$width = $this->get_item_width();
		$height = $this->get_item_height( $width );
		return sprintf( '.whp-modern-related-posts .mrpc-item { background-color: %1$s; width: %2$spx; height: %3$spx; } .whp-modern-related-posts .mrpc-item:hover .mrpc-overlay { background-color: %4$s } .whp-modern-related-posts .mrpc-link { width: %2$spx; height: %3$spx; } .whp-modern-related-posts .mrpc-link a { font-size: %5$spx; } .whp-modern-related-posts .owl-next { background-color: %6$s; }',
			mhpmrp_get_option( 'item_bg_color', '#000' ),
			$width,
			$height,
			mhpmrp_get_option( 'overlay_bg_color', '#dd3333' ),
			mhpmrp_get_option( 'link_size', 16 ),
			mhpmrp_get_option( 'nav_bg_color', '#000' )
		);
	}
	
	/**
	 * Get items number
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return int
	 */
	private function get_items_num() {
		return mhpmrp_get_option( 'display_num', 4 );
	}
	
	/**
	 * Get item width
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return int
	 */
	private function get_item_width() {
		global $content_width;
		$gutter = $this->get_item_gutter();
		$num = $this->get_items_num();
		$width_set = mhpmrp_get_option( 'content_width' );
		$width = ! empty( $width_set ) ? $width_set : $content_width;
		return absint( ( $width - $gutter * ( $num - 1) ) / $num );
	}
	
	/**
	 * Get item gutter
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return int
	 */
	private function get_item_gutter() {
		return 10;
	}	

	/**
	 * Get item height
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param int $width
	 * @return int
	 */	
	private function get_item_height( $width ) {
		return absint( $width * $this->get_item_aspect_ratio() );
	}
	
	/**
	 * Get item aspect ratio
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return float
	 */
	private function get_item_aspect_ratio() {
		$val = mhpmrp_get_option( 'aspect_ratio', '4:3' );
		if ( preg_match( '/(\d+)\:(\d+)/', $val, $match ) ) {
			return $match[2] / $match[1];
		}
		else {
			return 0.75;
		}
	}
	
	/**
	 * Print footer scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */		
	public function print_footer_scripts() {
?>
<script type="text/javascript">
jQuery(function($) {
	$( '.whp-modern-related-posts .mrpc-carousel' ).owlCarousel({
		items :					<?php echo $this->get_items_num(); ?>,
		theme :					'',
		autoPlay :				getAutoPlay(),
		stopOnHover : 			true,
		navigation :			true,
		navigationText :		["",""],
		slideSpeed :			500,
		itemsDesktop :			[1200,getNum( 1 )],
		itemsTablet :			[768,getNum( 2 )],
		itemsTabletSmall :		[580,2],
		itemsMobile :			[479,1]
	});
	
	function getNum( i ) {
		var num = parseInt( <?php echo $this->get_items_num(); ?> );
		if ( 0 >= num - i ) {
			return 1;
		}
		else
			return num - i;
	}
	
	function getAutoPlay() {
		var time = <?php echo mhpmrp_get_option( 'auto_play', 5000 ); ?>;
		if ( 0 >= time ) {
			return false;
		}
		return time;
	}
});
</script>
<?php
	}

}