<?php
/**
 * Get related posts query instance
 *
 * @since 1.0.0
 */
 
 class WHP_Modern_Related_Posts_Query {
 
 	/**
	 * Parameters
	 *
	 * @since 1.0.0
	 * @access private
	 * @type array $args
	 */
	private $args = array();
	
	/**
	 * Constructor, Set the parameters for class WP_Query()
	 *
	 * @link http://codex.wordpress.org/Class_Reference/WP_Query
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		$this->args = array(
			'post_type'				=> $this->get_post_type(),			
			'order'					=> $this->get_order(),
			'orderby'				=> $this->get_orderby(),
			'category__in'			=> $this->get_cats(),
  			'tag__in'				=> $this->get_tags(),
			'post__in'				=> $this->get_pids(),
			'post__not_in'			=> $this->get_not_pids(),
			'posts_per_page'		=> $this->get_posts_per_page(),
			'ignore_sticky_posts'	=> true
		);
	}	
	
	/**
	 * Get post types
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * return array
	 */
	private function get_post_type() {
		return array( 'post' );
	}	
	
	/**
	 * Get order argument
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * return string
	 */
	private function get_order() {
		return 'DESC';
	}

	/**
	 * Get orderby argument
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * return string
	 */	
	private function get_orderby() {
		if ( $this->get_pids() )
			return 'post__in';
		
		return mhpmrp_get_option( 'orderby', 'rand' );
	}
	
	/**
	 * Get posts_per_page argument
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * return int
	 */		
	private function get_posts_per_page() {
		return mhpmrp_get_option( 'posts_num', 10 );
	}
	
	/**
	 * Get category__in argument
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * return array
	 */		
	private function get_cats() {	
		if ( $this->get_pids() || 'category' != mhpmrp_get_option( 'related_via', 'tag' ) )
			return;
		
		$cats = get_the_category();
		$cat_ids = array();
		if ( $cats ) {
			foreach ( $cats as $cat ) {
				$cat_ids[] = $cat->term_id;
			}
		}
		
		return $cat_ids; 		
	}

	/**
	 * Get tag__in argument
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * return array
	 */	
	private function get_tags() {
		if ( $this->get_pids() || 'tag' != mhpmrp_get_option( 'related_via', 'tag' ) )
			return;		
		
		$tags = get_the_tags();
		$tag_ids = array();
		if ( $tags ) {
			foreach ( $tags as $tag ) {
				$tag_ids[] = $tag->term_id;
			}
		}
		
		if ( $this->get_pids() )
			return;		
		
		return $tag_ids;
	}

	/**
	 * Get post__in argument
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * return array
	 */	
	private function get_pids() {
		$pids = array();
		$pid_str = trim( get_post_meta( get_the_ID(), 'mrp_related_posts', true ) );
		if ( $pid_str ) {
			$pids = explode( ',', $pid_str );
		}

		return $pids;
	}
	
	/**
	 * Get post__not_in argument
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * return array
	 */	
	private function get_not_pids() {
		$pids = array( get_the_ID() );
		return $pids;
	}	

	/**
	 * Get WP_Query instance
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * return string
	 */		
	public function get_instance() {
		// The Query
		$query = new WP_Query( $this->args );
		return $query;
	}	
 
 }