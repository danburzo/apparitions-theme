<?php

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});
	
	add_filter('template_include', function($template) {
		return get_stylesheet_directory() . '/static/no-timber.html';
	});
	
	return;
}

Timber::$dirname = array('templates', 'views');

class ApparitionsSite extends TimberSite {

	function __construct() {
		add_theme_support( 'post-formats' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );

		add_filter( 'pre_get_posts', array ( $this, 'configure_get_posts' ) );

		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'init', array( $this, 'register_nav_menus' ) );
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		parent::__construct();
	}

	function register_post_types() {
		$this->register_post_type_member();
	}

	function register_taxonomies() {
		//this is where you can register custom taxonomies
	}

	function register_nav_menus() {
		register_nav_menu('main-menu',__( 'Main Menu' ));
		register_nav_menu('footer-menu',__( 'Footer Menu' ));
	}

	function register_shortcodes() {
		add_shortcode('readmore', array($this, 'shortcode_readmore'));
		add_shortcode('apparitions_members', array($this, 'shortcode_apparitions_members'));
	}

	function add_to_context( $context ) {
		
		$context['menu'] = new TimberMenu('main-menu');
		$context['footer_menu'] = new TimberMenu('footer-menu');
		$context['pagination'] = Timber::get_pagination();
		
		$context['site'] = $this;

		$context['is_home'] = is_home() || is_front_page();

		// add the WPML languages
		if (function_exists('icl_get_languages')) {
			$context['languages'] = icl_get_languages('skip_missing=0&orderby=code');
		}

		return $context;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own functions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter(new Twig_SimpleFilter('has_shortcode', array($this, 'filter_has_shortcode')));
		return $twig;
	}

	function filter_has_shortcode($post, $shortcode) {
        if (strpos($post->post_content, '[' . $shortcode)) {
            return true;
        }
		return false;
	}

	function register_post_type_member() {
		register_post_type('member', array(
			'labels' => array(
				'name' => 'Members',
				'singular_name' => 'Member'
			),
			'description' => 'Team & Organizer Bios',
			'rewrite' => array(
				'slug' => 'members'
			),
			'supports' => array(
				'title', 
				'editor', 
				'thumbnail', 
				'excerpt', 
				'custom-fields', 
				'page-attributes'
			),
			'taxonomies' => array(
				'categorie_proiect'
			),
			'public' => true,
			'has_archive' => false,
			'hierarchical' => false
		));
	}

	function configure_get_posts($query) {

	    // Don't alter queries in the admin interface
	    // and don't alter any query that's not the main one
	    if (is_admin() || !$query->is_main_query()) {
	        return;
	    } 

	    // for news articles, display 5 items per page
	    if ($query->is_category()) {
	        $query->set('posts_per_archive_page', 5);
	    }
	}

	function shortcode_readmore($atts = [], $content = '', $tag = '') {
		$context = array();

		// convert atts to lowercase
		$atts = array_change_key_case((array)$atts, CASE_LOWER);
		
		// default attributes
		$readmore_atts = shortcode_atts([
             'more' => 'Read more',
             'less' => 'Read less'
        ], $atts, $tag);

		$context['atts'] = $readmore_atts;
		$context['content'] = $content;
		$context['tag'] = $tag;

		// todo could do here 'shortcodes/' . $tag . '.twig', but ignore missing
		return Timber::compile('shortcodes/readmore.twig', $context);
	}

	function shortcode_apparitions_members($atts = [], $content = '', $tag = '') {
		$context = array();

		$context['members'] = Timber::get_posts(array(
			'post_type' => 'member',
			'orderby' => 'menu_order',
			'order' => 'ASC'
		));

		// todo could do here 'shortcodes/' . $tag . '.twig', but ignore missing
		return Timber::compile('shortcodes/apparitions_members.twig', $context);
	}
}

new ApparitionsSite();
