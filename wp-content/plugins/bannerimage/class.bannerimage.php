<?php
if ( !defined( 'BANNERIMAGE__PLUGIN_DIR' ) ) exit;
class Bannerimage {

	public function __construct(){
		
	}

	private static $initiated = false;
	public static function init() {
		if ( ! self::$initiated ) {
			self::create_db();
			self::plugin_activation();
			self::init_hooks();
			self::wpse4378_add_new_image_size();
			self::register_plugin_styles();
		}
	}

	private static function create_db(){
		global $wpdb;
		$tablename = $wpdb->prefix."bannerclicks";

		$sql = "CREATE TABLE `".$tablename."` (
          id int(11) NOT NULL AUTO_INCREMENT,
          banner_id int(11) NOT NULL,
          imressions int(11) NOT NULL  DEFAULT '0',
          clicks int(11) NOT NULL  DEFAULT '0',
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        $wpdb->query($sql);
        
	}

	private static function plugin_activation(){
		$labels = array(
			'name'               => esc_html__( 'Banner Image', 'post type general name', 'banner_image' ),
			'singular_name'      => esc_html__( 'Banner Image', 'post type singular name', 'banner_image' ),
			'menu_name'          => esc_html__( 'Banner Images', 'admin menu', 'banner_image' ),
			'name_admin_bar'     => esc_html__( 'Banner Image', 'add new on admin bar', 'banner_image' ),
			'add_new'            => esc_html__( 'Add New', 'Banner Image', 'banner_image' ),
			'add_new_item'       => esc_html__( 'Add New Banner Image', 'banner_image' ),
			'new_item'           => esc_html__( 'New Banner Image', 'banner_image' ),
			'edit_item'          => esc_html__( 'Edit Banner Image', 'banner_image' ),
			'view_item'          => esc_html__( 'View Banner Image', 'banner_image' ),
			'all_items'          => esc_html__( 'All Banner Images', 'banner_image' ),
			'search_items'       => esc_html__( 'Search Banner Image', 'banner_image' ),
			'parent_item_colon'  => esc_html__( 'Parent Banner Image:', 'banner_image' ),
			'not_found'          => esc_html__( 'No Banner Images found.', 'banner_image' ),
			'not_found_in_trash' => esc_html__( 'No Banner Images found in Trash.', 'banner_image' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'bannerimage' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'menu_icon'			 => 'dashicons-admin-home',
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		);

		register_post_type( 'bannerimage', $args );

		register_taxonomy(
			'bannercategory',
			'bannerimage',
			array(
				'label' => esc_html__( 'Banner Category', 'banner_image' ),
				'rewrite' => array( 'slug' => 'bannercategory' ),
				'hierarchical' => true,
			)
		);
	}


	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {

		add_action( 'add_meta_boxes',  array( 'Bannerimage', 'add_bannerimage_metaboxes' ) );
		add_action( 'save_post',  array( 'Bannerimage', 'save_custom_meta_box' ) );
		//add_action( 'wp_ajax_add_clicks',  array( 'Bannerimage', 'add_clicks' ) );
		//add_action( 'wp_ajax_nopriv_add_clicks',  array( 'Bannerimage', 'add_clicks' ) );

		add_filter( 'manage_taxonomies_for_bannerimage_columns', array( 'Bannerimage', 'bannerimage_type_columns' )  );
		add_filter( 'manage_edit-bannercategory_columns', array( 'Bannerimage', 'add_bannercategory_column_content' )  );
		add_action( "manage_bannercategory_custom_column",  array( 'Bannerimage', 'custom_column_content'),10,3);
		add_shortcode('banner-image', array( 'Bannerimage','call_bannerimage' ));

		add_filter('manage_bannerimage_posts_columns' , array( 'Bannerimage', 'book_cpt_columns'));
		add_action( 'manage_bannerimage_posts_custom_column' ,  array( 'Bannerimage', 'custom_columns'), 10, 2 );
		//add_action('manage_bannerimage_posts_columns', array( 'Bannerimage', 'manage_bannerimage_posts_columns'));
		/*add_shortcode('dashboardwedding', array( 'Bannerimage', 'dashboardwedding' ));
		add_shortcode('dashboardchecklist', array( 'Bannerimage', 'dashboardchecklist' ));		
		add_action( 'wp_ajax_gettips', array( 'Bannerimage','gettips') );*/
	}

	function register_plugin_styles(){
		wp_register_style( 'bannerimage-css', plugins_url( 'bannerimage/css/owl.carousel.css' ) );
		wp_register_style( 'bannertheme-css', plugins_url( 'bannerimage/css/owl.theme.default.css' ) );
		wp_register_style( 'bannertransition-css', plugins_url( 'bannerimage/css/animate.css' ) );
		wp_register_style( 'bannertype-css', plugins_url( 'bannerimage/css/side_tab.css' ) );
		wp_register_style( 'bannertype-scroll-css', plugins_url( 'bannerimage/css/jquery.mCustomScrollbar.css' ) );
		wp_register_script( 'bannerimage-jquery-js', "https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js");
		wp_register_script( 'bannerimage-js', plugins_url( 'bannerimage/js/owl.carousel.js' ) );
		wp_register_script( 'bannerimage-thumb-js', plugins_url( 'bannerimage/js/owl.carousel2.thumbs.js' ) );
		wp_register_script( 'bannerimage-scroll-js', plugins_url( 'bannerimage/js/jquery.mCustomScrollbar.js' ) );
		wp_register_script( 'bannerimage-scrollconcat-js', plugins_url( 'bannerimage/js/jquery.mCustomScrollbar.concat.min.js' ) );
	}

	function wpse4378_add_new_image_size() {
	   // add_image_size( 'banenr-image-large', 1080 , 380, true ); //mobile
	    add_image_size( 'banenr-image-default', 960 , 420, true ); //mobile
	  //  add_image_size( 'banenr-image-small', 640 , 380, true ); //mobile
	}

	function book_cpt_columns($columns) {
		$new_columns = array(
			'imressions' => __('Imressions', 'imressions'),
			'banner_max' => __('Max Imressions', 'banner_image'),
			'banner_clicks' => __('Click Count', 'banner_image'),
			'banner_ctr' => __('CTR (%)', 'banner_image'),
		);

	   return array_merge($columns, $new_columns);
	}

	function custom_columns( $column, $post_id ) {

		$imp = get_post_meta( $post_id, 'imressions', true ) ?: 0;
		$max = get_post_meta( $post_id, 'banner_max', true ) ?: 0;
		$c = get_post_meta( $post_id, 'clicks_count', true ) ?: 0;
		$ctr = number_format(($c*100/$imp),2,".",".");

		switch ( $column ) {
			case 'imressions':
				echo $imp;
				break;

			case 'banner_max':
				echo $max;
				break;

			case 'banner_clicks':
				echo $c; 
				break;	

			case 'banner_ctr':
				echo $ctr; 
				break;
		}
	}

	function add_bannercategory_column_content( $columns ){
		$columns['short_code'] = 'Short Code';
		return $columns;
	}
	//add_filter( "manage_edit-bannercategory_columns", 'custom_column_header', 10);

	function bannerimage_type_columns( $taxonomies ) {
	    $taxonomies[] = 'bannercategory';
	    return $taxonomies;
	}

	function custom_column_content( $value, $column_name, $tax_id ){
		//for multiple custom column, you may consider using the column name to distinguish
		 if ($column_name === 'short_code') {
			 echo '[banner-image catid='.$tax_id.']';
		 }
		 return $columns;
	}

	function add_bannerimage_metaboxes() {
	    add_meta_box('bannerimage_date', 'Banner Details', array('Bannerimage','banner_title'), 'bannerimage', 'normal', 'default');
	}

	function call_bannerimage($atts){
	     extract(shortcode_atts(array(
	                  'catid' => 0,
	                  'type' => 0,
	                  'items' => 2,
	               ), $atts));
	    
	    if(!empty($atts['catid'])){
	    	$term_id = $atts['catid'];	
	    }

	    $args = array(
	        'posts_per_page' => -1,
	        'post_type' => 'bannerimage',
	        'orderby' => 'meta_value_num',
  			'meta_key' => 'banner_sort',
  			'order' => 'ASC',
	        'tax_query' => array(
	            array(
	                'taxonomy' => 'bannercategory',
	                'field' => 'term_id',
	                'terms' => $term_id,
	            )
	        )
	    );
	    
	    $posts = get_posts( $args );
		$result = Bannerimage::default_slider($posts,$type,$term_id,$items);
	    
	    return $result;
	}

	function insert_impression($post_id){
		global $wpdb;
		$tablename = $wpdb->prefix."bannerclicks";

		$sql = "SELECT * FROM $tablename where banner_id = $post_id";
		$select_exists = $wpdb->get_row($sql);
		if (empty($select_exists)) {
			$args = array(
				"banner_id" => $post_id,
				"imressions" => 1
			);

			$qr = $wpdb->insert($tablename,$args);
			update_post_meta( $post_id, "imressions", 1 );
		}else{
			$impression = ($select_exists->imressions) + 1;
			$args = array(
				"id" => $select_exists->id,
				"banner_id" => $post_id,
				"imressions" => $impression,
			);
			update_post_meta( $post_id, "imressions", $impression );
			$qr = $wpdb->update($tablename,$args,array( 'id' => $select_exists->id ));	
		}
		
	}

	
	function add_clicks(){
		global $wpdb;
		$tablename = $wpdb->prefix."bannerclicks";
		$result = array();
		$post_id = $_POST['bannerid'];
		$sql = "SELECT * FROM $tablename where banner_id = $post_id";
		$select_exists = $wpdb->get_row($sql);
		if (empty($select_exists)) {
			$args = array(
				"banner_id" => $post_id,
				"clicks_count" => 1
			);

			$qr = $wpdb->insert($tablename,$args);
			if($qr){
				update_post_meta( $post_id, "clicks_count", 1 );
				$result['success'] = "1";
			}else{
				$result['success'] = "0";
			}
		}else{
			$clicks_count = ($select_exists->clicks_count) + 1;
			$args = array(
				"id" => $select_exists->id,
				"banner_id" => $post_id,
				"clicks_count" => $clicks_count,
			);
			
			$qr = $wpdb->update($tablename,$args,array( 'id' => $select_exists->id ));	
			if($qr){
				update_post_meta( $post_id, "clicks_count", $clicks_count );
				$result['success'] = "1";
			}else{
				$result['success'] = "0";
			}
		}
		
	}

	function default_slider($data,$type=0,$term_id=0,$items){
		
		wp_enqueue_style("bannerimage-css");
		wp_enqueue_style("bannertheme-css");
		wp_enqueue_style("bannertransition-css");
		wp_enqueue_style("bannertype-css");
		wp_enqueue_style("bannertype-scroll-css");
	    wp_enqueue_script('bannerimage-jquery-js');
	    wp_enqueue_script('bannerimage-js');
	    wp_enqueue_script('bannerimage-thumb-js');
	    wp_enqueue_script('bannerimage-scrollconcat-js');

		$outpot = '';
		$outpot .= '<div class="banner-image">';

	    if($type == 1){
	    	/* Carousel Banner */

	    	if(isset($_POST['bannerid']) && $_POST['bannerid'] != ""){
	    		if (! isset( $_POST['banner_nounce'] ) 
					|| ! wp_verify_nonce( $_POST['banner_nounce'], 'add_clicks' ) 
				) {
				   print 'Sorry, your nonce did not verify.';
				   exit;
				} else {
				   Bannerimage::add_clicks();
				}
	    	}

			$outpot .= '<h3> Carousel Banner</h3>';
			$outpot .='<div class="banner-carousel owl-carousel owl-theme" style="display:block">';
			foreach ($data as $post) {

				$banner_detail = Bannerimage::get_banner_detail($post->ID);
				$banner_link = get_post_meta($post->ID,'banner_link',true);
				$banner_target = get_post_meta($post->ID,'banner_target',true);
				$banner_max = get_post_meta($post->ID,'banner_max',true);
				$banner_from = get_post_meta($post->ID,'from_date',true);
				$banner_to = get_post_meta($post->ID,'to_date',true);
				$banner_sort = get_post_meta($post->ID,'banner_sort',true);
				$banner_slide_type = get_post_meta($post->ID,'banner_slide_type',true);
				$banner_video_url = get_post_meta($post->ID,'banner_video_url',true);
				$banner_impression = $banner_detail->imressions;

				$max = Bannerimage::check_bannerdmax($banner_impression,$banner_max);
				$date = Bannerimage::check_bannerdate($banner_from,$banner_to);

				if($max == 0){
					continue;
				}
				if($date == 0){
					continue;
				}

				$outpot .= '<form id="carousel_banner'.$post->ID.'_'.$term_id.'" method="post" name="carousel_banner'.$post->ID.'_'.$term_id.'">
				<input name="bannerid" type="hidden" value="'.$post->ID.'" />';

				$outpot .= wp_nonce_field( 'add_clicks', 'banner_nounce' );

				if($banner_slide_type == 1 && !empty($banner_video_url)){
					$outpot .='<div class="item-video" >
					<a class="owl-video" href="'.$banner_video_url.'"></a></div>';
				}else{
					$outpot .='<div class="item" style="width:100%;">
				<a bannerid="bannerurl'.$post->ID.'" class="bannerurl" href="'.$banner_link.'" target="'.$banner_target.'" alt="'.get_the_title($post->ID).'">
					<img src="'.get_the_post_thumbnail_url($post->ID,'banenr-image-default').'" alt="'.get_the_title($post->ID).'">
					</a>
					</div>';	
				}

				$outpot .= '</form>';
				if(!isset($_POST['bannerid']) && $_POST['bannerid'] == ""){
					Bannerimage::insert_impression($post->ID);
				}
			}
			$outpot .= '</div>';

			$outpot .= '<script>
			jQuery(document).ready(function($) {
				jQuery(".banner-carousel").owlCarousel({
					loop:true,
				    margin:10,
				    video:true,
				    loop:true,
				    nav:false,
				    items:'.$items.'
				})

				/* Start Click Count */

					jQuery(".bannerurl").click(function(e) {
						var form_id = jQuery(this).parent().parent().attr("id");
						var targeturl = jQuery(this).attr("href");
						var target = jQuery(this).attr("target");
						console.log(form_id);
						if(targeturl != ""){
							jQuery("#"+form_id).submit();
						}else{
							e.preventDefault();
						}
					});

				/* Finish Click Count */

			});
			</script>';
			/* Carousel Banner */
	    }else if($type == 2){
	    	/* Carousel with Side block */

	    	if(isset($_POST['bannerid']) && $_POST['bannerid'] != ""){
	    		if (! isset( $_POST['banner_nounce'] ) 
					|| ! wp_verify_nonce( $_POST['banner_nounce'], 'add_clicks' ) 
				) {
				   print 'Sorry, your nonce did not verify.';
				   exit;
				} else {
				   Bannerimage::add_clicks();
				}
	    	}

			$outpot .= '<h3>Carousel with Side block</h3>';
			$outpot .='<div id="type2" class="owl-carousel side-block owl-theme" style="display:block">';
			foreach ($data as $post) {

				$banner_detail = Bannerimage::get_banner_detail($post->ID);
				$banner_link = get_post_meta($post->ID,'banner_link',true);
				$banner_target = get_post_meta($post->ID,'banner_target',true);
				$banner_max = get_post_meta($post->ID,'banner_max',true);
				$banner_from = get_post_meta($post->ID,'from_date',true);
				$banner_to = get_post_meta($post->ID,'to_date',true);
				$banner_sort = get_post_meta($post->ID,'banner_sort',true);
				$banner_slide_type = get_post_meta($post->ID,'banner_slide_type',true);
				$banner_video_url = get_post_meta($post->ID,'banner_video_url',true);
				$banner_impression = $banner_detail->imressions;

				$max = Bannerimage::check_bannerdmax($banner_impression,$banner_max);
				$date = Bannerimage::check_bannerdate($banner_from,$banner_to);

				if($max == 0){
					continue;
				}
				if($date == 0){
					continue;
				}

				$outpot .= '<form id="sideblock_banner'.$post->ID.'_'.$term_id.'" method="post" name="sideblock_banner'.$post->ID.'_'.$term_id.'">
				<input name="bannerid" type="hidden" value="'.$post->ID.'" />';

				$outpot .= wp_nonce_field( 'add_clicks', 'banner_nounce' );

				if($banner_slide_type == 1 && !empty($banner_video_url)){
					$outpot .='<div class="item-video" ><a class="owl-video" href="'.$banner_video_url.'"></a></div>';
				}else{
					$outpot .='<div class="item" style="width:100%;">
				<a bannerid="bannerurl'.$post->ID.'" class="bannerurl" href="'.$banner_link.'" target="'.$banner_target.'" alt="'.get_the_title($post->ID).'">
					<img src="'.get_the_post_thumbnail_url($post->ID,'banenr-image-default').'" alt="'.get_the_title($post->ID).'">
					</a>
					</div>';	
				}

				$outpot .= '</form>';
				if(!isset($_POST['bannerid']) && $_POST['bannerid'] == ""){
					Bannerimage::insert_impression($post->ID);
				}
			}
			$outpot .= '</div>';
			$outpot .= "<div class='side-nav'><ul id='carousel-custom-dots' class='owl-dots'>";
			foreach ($data as $post1) {
				$outpot .= "<li class='owl-dot'><img src='".get_the_post_thumbnail_url($post1->ID,'banenr-image-default')."' alt='".get_the_title($post1->ID)."'></li>";
			}
			$outpot .=	'</ul></div>';
			$outpot .= '<script>
			jQuery(document).ready(function($) {
			var owl = jQuery(".side-block");
		    owl.owlCarousel({
		        loop: true,
		        items: 1,
		        nav:false,
		        video:true,
		        dotsContainer: "#carousel-custom-dots",
		        dots:true
		    });

		    jQuery(".side-nav").mCustomScrollbar({
		    	theme:"dark"
		    });
			
			/* Start Click Count */

				jQuery(".bannerurl").click(function(e) {
					var form_id = jQuery(this).parent().parent().attr("id");
					var targeturl = jQuery(this).attr("href");
					var target = jQuery(this).attr("target");
					console.log(form_id);
					if(targeturl != ""){
						jQuery("#"+form_id).submit();
					}else{
						e.preventDefault();
					}
				});

			/* Finish Click Count */

			jQuery(".owl-dot").click(function () {
			    owl.trigger("to.owl.carousel", [jQuery(this).index(), 300]);
			});

			});
			</script>';
	    	/* Carousel with Side block */
	    }else if($type == 3){
	    	/* Carousel with Tab */

	    	if(isset($_POST['bannerid']) && $_POST['bannerid'] != ""){
	    		if (! isset( $_POST['banner_nounce'] ) 
					|| ! wp_verify_nonce( $_POST['banner_nounce'], 'add_clicks' ) 
				) {
				   print 'Sorry, your nonce did not verify.';
				   exit;
				} else {
				   Bannerimage::add_clicks();
				}
	    	}

			$outpot .= '<h3>Carousel with Tab</h3>';
			$outpot .='<div class="owl-carousel tab-block owl-theme" style="display:block">';
			foreach ($data as $post) {

				$banner_detail = Bannerimage::get_banner_detail($post->ID);
				$banner_link = get_post_meta($post->ID,'banner_link',true);
				$banner_target = get_post_meta($post->ID,'banner_target',true);
				$banner_max = get_post_meta($post->ID,'banner_max',true);
				$banner_from = get_post_meta($post->ID,'from_date',true);
				$banner_to = get_post_meta($post->ID,'to_date',true);
				$banner_sort = get_post_meta($post->ID,'banner_sort',true);
				$banner_slide_type = get_post_meta($post->ID,'banner_slide_type',true);
				$banner_video_url = get_post_meta($post->ID,'banner_video_url',true);
				$banner_impression = $banner_detail->imressions;

				$max = Bannerimage::check_bannerdmax($banner_impression,$banner_max);
				$date = Bannerimage::check_bannerdate($banner_from,$banner_to);

				if($max == 0){
					continue;
				}
				if($date == 0){
					continue;
				}

				$outpot .= '<form id="tabbed_banner'.$post->ID.'_'.$term_id.'" method="post" name="tabbed_banner'.$post->ID.'_'.$term_id.'">
				<input name="bannerid" type="hidden" value="'.$post->ID.'" />';

				$outpot .= wp_nonce_field( 'add_clicks', 'banner_nounce' );

				if($banner_slide_type == 1 && !empty($banner_video_url)){
					$outpot .='<div class="item-video"  style="width:100%;"><a class="owl-video" href="'.$banner_video_url.'"></a></div>';
				}else{
					$outpot .='<div class="item" style="width:100%;">
				<a bannerid="bannerurl'.$post->ID.'" class="bannerurl" href="'.$banner_link.'" target="'.$banner_target.'" alt="'.get_the_title($post->ID).'">
					<img src="'.get_the_post_thumbnail_url($post->ID,'banenr-image-default').'" alt="'.get_the_title($post->ID).'">
					</a>
					</div>';	
				}

				$outpot .= '</form>';
				if(!isset($_POST['bannerid']) && $_POST['bannerid'] == ""){
					Bannerimage::insert_impression($post->ID);
				}
			}
			$outpot .= '</div>';
			$outpot .= "<div class='banner-nav'><ul id='carousel-custom-dots' class='owl-dots'>";
			foreach ($data as $post1) {
				$outpot .= "<li class='owl-dot'>".get_the_title($post1->ID)."</li>";
			}
			$outpot .=	'</ul></div>';

			$outpot .= '<script>
			jQuery(document).ready(function($) {
			var owl = jQuery(".tab-block");
		    owl.owlCarousel({
		        loop: true,
		        items: 1,
		        video:true,
		        nav:false,
		        dots:true,
		        dotsContainer: "#carousel-custom-dots"
		    });

		    jQuery(".owl-dot").click(function () {
			    owl.trigger("to.owl.carousel", [jQuery(this).index(), 300]);
			});

		    jQuery(".banner-nav").mCustomScrollbar({
		    	axis:"x",
		    	theme:"dark"
		    });	

			/* Start Click Count */

				jQuery(".bannerurl").click(function(e) {
					var form_id = jQuery(this).parent().parent().attr("id");
					var targeturl = jQuery(this).attr("href");
					var target = jQuery(this).attr("target");
					console.log(form_id);
					if(targeturl != ""){
						jQuery("#"+form_id).submit();
					}else{
						e.preventDefault();
					}
				});

			/* Finish Click Count */

			});
			</script>';
			/* Carousel with Tab */
	    }else if($type == 4){
	    	/* Single Banner */

	    	if(isset($_POST['bannerid']) && $_POST['bannerid'] != ""){
	    		if (! isset( $_POST['banner_nounce'] ) 
					|| ! wp_verify_nonce( $_POST['banner_nounce'], 'add_clicks' ) 
				) {
				   print 'Sorry, your nonce did not verify.';
				   exit;
				} else {
				   Bannerimage::add_clicks();
				}
	    	}

			$outpot .= '<h3>Single Banner</h3>';
			$outpot .='<div class="owl-carousel single-banner owl-theme" style="display:block">';
			$post = array_rand($data);
			$post_id = $data[$post]->ID;

			$banner_detail = Bannerimage::get_banner_detail($post_id);
			$banner_link = get_post_meta($post_id,'banner_link',true);
			$banner_target = get_post_meta($post_id,'banner_target',true);
			$banner_max = get_post_meta($post_id,'banner_max',true);
			$banner_from = get_post_meta($post_id,'from_date',true);
			$banner_to = get_post_meta($post_id,'to_date',true);
			$banner_sort = get_post_meta($post_id,'banner_sort',true);
			$banner_slide_type = get_post_meta($post_id,'banner_slide_type',true);
			$banner_video_url = get_post_meta($post_id,'banner_video_url',true);
			$banner_impression = $banner_detail->imressions;

			$max = Bannerimage::check_bannerdmax($banner_impression,$banner_max);
			$date = Bannerimage::check_bannerdate($banner_from,$banner_to);

			if($max == 0){
				$post = array_rand($data);
				$post_id = $data[$post]->ID;
			}
			if($date == 0){
				$post = array_rand($data);
				$post_id = $data[$post]->ID;
			}

			//echo $banner_link;exit;

			$outpot .= '<form id="single_banner'.$post_id.'_'.$term_id.'" method="post" name="single_banner'.$post_id.'_'.$term_id.'">
				<input name="bannerid" type="hidden" value="'.$post_id.'" />';

			$outpot .= wp_nonce_field( 'add_clicks', 'banner_nounce' );

			//foreach ($data as $post) {
		

			if($banner_slide_type == 1 && !empty($banner_video_url)){
				$outpot .='<div class="item-video" ><a class="owl-video" href="'.$banner_video_url.'"></a></div>';
			}else{
				$outpot .='<div class="item" style="width:100%;">
			<a bannerid="bannerurl'.$post_id.'" class="bannerurl" href="'.$banner_link.'" target="'.$banner_target.'" alt="'.get_the_title($post_id).'">
				<img src="'.get_the_post_thumbnail_url($post_id,'banenr-image-default').'" alt="'.get_the_title($post_id).'">
				</a>
				</div>';	
			}	
				$outpot .= '</form>';
				if(!isset($_POST['bannerid']) && $_POST['bannerid'] == ""){
					Bannerimage::insert_impression($post_id);
				}
			//}
			$outpot .= '</div>';

			$outpot .= '<script>
			jQuery(document).ready(function($) {
		    jQuery(".single-banner").owlCarousel({
		        loop: false,
		        items: 1,
		        video:true,
		        nav:false
		    });

		    /* Start Click Count */

				jQuery(".bannerurl").click(function(e) {
					var form_id = jQuery(this).parent().parent().attr("id");
					var targeturl = jQuery(this).attr("href");
					var target = jQuery(this).attr("target");
					console.log(form_id);
					if(targeturl != ""){
						jQuery("#"+form_id).submit();
					}else{
						e.preventDefault();
					}
				});

			/* Finish Click Count */

			});
			</script>';
			/* Single Banner */
	    }else{ 
	    	/* Default Banner */
	    	if(isset($_POST['bannerid']) && $_POST['bannerid'] != ""){
	    		if (! isset( $_POST['banner_nounce'] ) 
					|| ! wp_verify_nonce( $_POST['banner_nounce'], 'add_clicks' ) 
				) {
				   print 'Sorry, your nonce did not verify.';
				   exit;
				} else {
				   Bannerimage::add_clicks();
				}
	    	}
	    	
	    	$outpot .= '<h3> Default Banner</h3>';
			$outpot .='<div id="defaultbanner'.$term_id.'" class="default-banner owl-carousel owl-theme" style="display:block">';

			foreach ($data as $post) {

				$banner_detail = Bannerimage::get_banner_detail($post->ID);
				$banner_link = get_post_meta($post->ID,'banner_link',true);
				$banner_target = get_post_meta($post->ID,'banner_target',true);
				$banner_max = get_post_meta($post->ID,'banner_max',true);
				$banner_from = get_post_meta($post->ID,'from_date',true);
				$banner_to = get_post_meta($post->ID,'to_date',true);
				$banner_sort = get_post_meta($post->ID,'banner_sort',true);
				$banner_slide_type = get_post_meta($post->ID,'banner_slide_type',true);
				$banner_video_url = get_post_meta($post->ID,'banner_video_url',true);
				$banner_impression = $banner_detail->imressions;

				$max = Bannerimage::check_bannerdmax($banner_impression,$banner_max);
				$date = Bannerimage::check_bannerdate($banner_from,$banner_to);

				if($max == 0){
					continue;
				}
				if($date == 0){
					continue;
				}

				$outpot .= '<form id="default_banner'.$post->ID.'_'.$term_id.'" method="post" name="default_banner'.$post->ID.'_'.$term_id.'">
				<input name="bannerid" type="hidden" value="'.$post->ID.'" />';

				$outpot .= wp_nonce_field( 'add_clicks', 'banner_nounce' );
				if($banner_slide_type == 1 && !empty($banner_video_url)){
					$outpot .='<div class="item-video" >
					<a bannerid="bannerurl'.$post->ID.'" class="bannerurl owl-video" href="'.$banner_video_url.'"></a>
					<div  href="'.$banner_video_url.'"></div>
					</div>';
				}else{
					$outpot .='<div class="item" style="width:100%;">
				<a bannerid="bannerurl'.$post->ID.'" class="bannerurl" href="'.$banner_link.'" target="'.$banner_target.'" alt="'.get_the_title($post->ID).'">
					<img src="'.get_the_post_thumbnail_url($post->ID,'banenr-image-default').'" alt="'.get_the_title($post->ID).'">
					</a>
					</div>';	
				}
				
				$outpot .= '</form>';
				if(!isset($_POST['bannerid']) && $_POST['bannerid'] == ""){
					Bannerimage::insert_impression($post->ID);
				}
			}
			$outpot .= '</div>';

			$outpot .= '<script>
			jQuery(document).ready(function($) {
				jQuery(".default-banner").owlCarousel({
				    items:1,
				    video:true,
				    loop:true,
				    smartSpeed:450,
				    nav:false
				})

				/* Start Click Count */

				jQuery(".bannerurl").click(function(e) {
					var form_id = jQuery(this).parent().parent().attr("id");
					var targeturl = jQuery(this).attr("href");
					var target = jQuery(this).attr("target");
					if(targeturl != ""){
						jQuery("#"+form_id).submit();
					}else{
						e.preventDefault();
					}
				});

				/* Finish Click Count */
			});

			</script>';
			/* Default Banner */
	    }
		$outpot .= '</div>';
		return $outpot;
	}

	function check_bannerdate($from,$to)
	{	
		$current_date = date("d/m/Y");
		
		if(empty($from)){
			$from = date("d/m/Y");
		}

		if(empty($to)){
			$to = date("d/m/Y");
		}

		if($current_date <= $to && $current_date >= $from){
			return 1;
		}else{
			return 0;
		}
		
	}

	function check_bannerdmax($banner_impression,$banner_max)
	{	
		if(!empty($banner_max)){
			$banner_max = $banner_max;
		}else{
			$banner_max = 0;
		}
		if($banner_impression > $banner_max && $banner_max != 0){
			return 0;
		}else{
			return 1;
		}
	}

	function get_banner_detail($bannerid)
	{	
		global $wpdb;
		$tablename = $wpdb->prefix."bannerclicks";
		$bannerDetail = $wpdb->get_row("SELECT * from $tablename where banner_id = $bannerid");
		return $bannerDetail;
	}

	function banner_title()
	{
		global $post;
	    $values = get_post_custom( $post->ID );
	    $title = isset( $values['banner_title'] ) ? $values['banner_title'][0] : '';
	    $desc = isset( $values['banner_desc'] ) ? $values['banner_desc'][0] : '';
	    $link = isset( $values['banner_link'] ) ? $values['banner_link'][0] : '';
	    $banner_ext = isset( $values['banner_ext'] ) ? $values['banner_ext'][0] : '';
	    $banner_target = isset( $values['banner_target'] ) ? $values['banner_target'][0] : '';
	    $banner_sort = isset( $values['banner_sort'] ) ? $values['banner_sort'][0] : '';
	    $from_date = isset( $values['from_date'] ) ? $values['from_date'][0] : '';
	    $to_date = isset( $values['to_date'] ) ? $values['to_date'][0] : '';
	    $banner_max = isset( $values['banner_max'] ) ? $values['banner_max'][0] : '';
	    $banner_video_url = isset( $values['banner_video_url'] ) ? $values['banner_video_url'][0] : '';
	    $banner_slide_type = isset( $values['banner_slide_type'] ) ? $values['banner_slide_type'][0] : '';
	    
	    // We'll use this nonce field later on when saving.
	    wp_nonce_field(basename(__FILE__), "meta-box-nonce");

	    wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
	    ?>

	    <p>
	        <h4 for="banner_link"><?php _e('Web Link','banner_image');?></h4>
	        <input size="30" spellcheck="true" type="text" name="banner_link" id="banner_link" value="<?php echo __($link,'banner_image'); ?>" style="width: 100%" />
	    </p>

	    <p>
	        <h4 for="banner_slide_type"><?php _e('Select Type','banner_image');?></h4>
	        <select name="banner_slide_type" id="banner_slide_type">
	        	<?php
	        	$option_values = array(
	        			__('Image','banner_image')=> __('0','banner_image'),
	        			__('Video','banner_image') => __('1','banner_image'),
	        		);
	        	foreach($option_values as $key => $value) 
                {
                    if($value == $banner_slide_type)
                    {
                    ?>
                        <option selected value="<?php echo __($value,'banner_image'); ?>" ><?php echo __($key,'banner_image'); ?></option>
                    <?php    
                    }
                    else
                    {
                    ?>
                        <option value="<?php echo __($value,'banner_image'); ?>"><?php echo __($key,'banner_image'); ?></option>
                    <?php
                    }
                }
                ?>
	        </select>
	    </p>

	    <p>
	        <h4 for="banner_video_url"><?php _e('Video URL','banner_image');?></h4>
	        <input size="30" spellcheck="true" type="text" name="banner_video_url" id="banner_video_url" value="<?php echo __($banner_video_url,'banner_image'); ?>" style="width: 100%" />
	    </p>

	    <p>
	        <h4 for="banner_target"><?php _e('Select Target','banner_image');?></h4>
	        <select name="banner_target" id="banner_ext">
	        	<?php
	        	$option_values = array(
	        			__('_self (in current window)','banner_image')=> __('_self','banner_image'),
	        			__('_blank (in new window)','banner_image') => __('_blank','banner_image'),
	        			__('_self (in own frameset)','banner_image') => __('_parent','banner_image'),
	        			__('_top (in full current browser window)','banner_image') => __('_top','banner_image')
	        		);
	        	foreach($option_values as $key => $value) 
                {
                    if($value == $banner_ext)
                    {
                    ?>
                        <option selected value="<?php echo __($value,'banner_image'); ?>" ><?php echo __($key,'banner_image'); ?></option>
                    <?php    
                    }
                    else
                    {
                    ?>
                        <option value="<?php echo __($value,'banner_image'); ?>"><?php echo __($key,'banner_image'); ?></option>
                    <?php
                    }
                }
                ?>
	        </select>
	    </p>
		
		<p>
	        <h4 for="banner_sort"><?php _e('Max Impression Limit','banner_image');?></h4>
	        <input size="30" spellcheck="true" type="text" name="banner_max" id="banner_max" value="<?php echo __($banner_max,'banner_image'); ?>" style="width: 100%" />
	    </p>

	    <p>
	        <h4 for="banner_sort"><?php _e('Sort Order','banner_image');?></h4>
	        <input size="30" spellcheck="true" type="text" name="banner_sort" id="banner_sort" value="<?php echo __($banner_sort,'banner_image'); ?>" style="width: 100%" />
	    </p>

	    <p>
	        <h4 for="banner_sort"><?php _e('Active From','banner_image');?></h4>
	        <input type="text" name="from_date" id="from_date" value="<?php echo $from_date; ?>" />
	    </p>

	    <p>
	        <h4 for="banner_sort"><?php _e('Active To','banner_image');?></h4>
	        <input type="text" name="to_date" id="to_date" value="<?php echo $to_date; ?>" />
	    </p>

	<script>
	jQuery(document).ready(function(){
		jQuery('#from_date').datepicker({
			dateFormat : 'dd/mm/yy'
		});

		jQuery('#to_date').datepicker({
			dateFormat : 'dd/mm/yy'
		});
	});
	</script>
	    <?php  
	}

	function save_custom_meta_box($post_id)
	{
		global $post;
		
	    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
	        return $post_id;
	    if(!current_user_can("edit_post", $post_id))
	        return $post_id;
	    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
	        return $post_id;
	    $slug = "bannerimage";
	   
	    if($slug != $post->post_type)
	        return $post_id;

	    $banner_link = "";
	    $banner_ext = "";
	    $banner_target = "";
	    $banner_sort = "";
	    $from_date = "";
	    $to_date = "";
	    $banner_max = "";
	    $banner_slide_type = "";
	    $banner_video_url = "";
	    
	    if(isset($_POST["banner_link"]))
	    {
	        $banner_link = $_POST["banner_link"];
	    }   
	    update_post_meta($post_id, "banner_link", $banner_link);

	    if(isset($_POST["banner_slide_type"]))
	    {
	        $banner_slide_type = $_POST["banner_slide_type"];
	    }   
	    update_post_meta($post_id, "banner_slide_type", $banner_slide_type);

	    if(isset($_POST["banner_video_url"]))
	    {
	        $banner_video_url = $_POST["banner_video_url"];
	    }   
	    update_post_meta($post_id, "banner_video_url", $banner_video_url);

	    if(isset($_POST["banner_target"]))
	    {
	        $banner_target = $_POST["banner_target"];
	    }
	    update_post_meta($post_id, "banner_target", $banner_target);

	    if(isset($_POST["banner_sort"]))
	    {
	        $banner_sort = $_POST["banner_sort"];
	    }
	    update_post_meta($post_id, "banner_sort", $banner_sort);

	    if(isset($_POST["banner_sort"]))
	    {
	        $banner_sort = $_POST["banner_sort"];
	    }
	    update_post_meta($post_id, "banner_sort", $banner_sort);

	    if(isset($_POST["from_date"]))
	    {
	        $from_date = $_POST["from_date"];
	    }
	    update_post_meta($post_id, "from_date", $from_date);

	    if(isset($_POST["to_date"]))
	    {
	        $to_date = $_POST["to_date"];
	    }
	    update_post_meta($post_id, "to_date", $to_date);

	    if(isset($_POST["banner_max"]))
	    {
	        $banner_max = $_POST["banner_max"];
	    }
	    update_post_meta($post_id, "banner_max", $banner_max);
	    
	}

}