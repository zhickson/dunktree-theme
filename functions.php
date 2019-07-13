<?php
/**
 * Dunktree functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Dunktree
 */

if ( ! function_exists( 'dunktree_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function dunktree_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Dunktree, use a find and replace
		 * to change 'dunktree' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'dunktree', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'dunktree' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		/**
		 * Add support for default block styles.
		 *
		 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/#default-block-styles
		 */
		//add_theme_support( 'wp-block-styles' );

		/**
		 * Add support for wide aligments.
		 *
		 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/#wide-alignment
		 */
		add_theme_support( 'align-wide' );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'dunktree_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 190,
			'width'       => 25,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'dunktree_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function dunktree_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'dunktree_content_width', 640 );
}
add_action( 'after_setup_theme', 'dunktree_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function dunktree_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'dunktree' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'dunktree' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'dunktree_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function dunktree_scripts() {

	wp_enqueue_style( 'typekit', 'https://use.typekit.net/xtf2qsk.css', false, false, null );
	
	// Get the correct asset paths that were built during deployment
	$manifest = json_decode( file_get_contents( 'dist/assets.json', true ) );
		// Map JSON object to variables
		$core  = $manifest->core;

	// Core Styles and Scripts
	wp_enqueue_style( 'core-styles', get_template_directory_uri() . "/dist/" . $core->css, false, null );
	wp_enqueue_script( 'core-scripts', get_template_directory_uri() . "/dist/" . $core->js, [ 'jquery' ], null, true );
	wp_localize_script( 'core-scripts' , 'ajax_js', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'dunktree_scripts' );

/**
 * Clean up wp_head()
 *
 * Remove unnecessary <link>'s
 * Remove inline CSS and JS from WP emoji support
 * Remove inline CSS used by Recent Comments widget
 * Remove inline CSS used by posts with galleries
 * Remove self-closing tag
 *
 * @link https://github.com/roots/soil/blob/master/modules/clean-up.php
 */
function dunktree_head_cleanup() {
	// Originally from http://wpengineer.com/1438/wordpress-header/
	remove_action('wp_head', 'feed_links_extra', 3);
	add_action('wp_head', 'ob_start', 1, 0);
	add_action('wp_head', function () {
	  $pattern = '/.*' . preg_quote(esc_url(get_feed_link('comments_' . get_default_feed())), '/') . '.*[\r\n]+/';
	  echo preg_replace($pattern, '', ob_get_clean());
	}, 3, 0);
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'wp_shortlink_wp_head', 10);
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_action('wp_head', 'wp_oembed_add_discovery_links');
	remove_action('wp_head', 'wp_oembed_add_host_js');
	remove_action('wp_head', 'rest_output_link_wp_head', 10);
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	add_filter('use_default_gallery_style', '__return_false');
	add_filter('emoji_svg_url', '__return_false');
	add_filter('show_recent_comments_widget_style', '__return_false');
}
add_action('init', 'dunktree_head_cleanup' );

/**
 * Remove the WordPress version from RSS feeds
 */
add_filter('the_generator', '__return_false');

/**
 * Clean up language_attributes() used in <html> tag
 *
 * Remove dir="ltr"
 */
function dunktree_language_attributes() {
	$attributes = [];
	if (is_rtl()) {
		$attributes[] = 'dir="rtl"';
	}
	$lang = get_bloginfo('language');
	if ($lang) {
		$attributes[] = "lang=\"$lang\"";
	}
	$output = implode(' ', $attributes);
	$output = apply_filters('dunktree_language_attributes', $output);
	return $output;
}
add_filter('language_attributes', 'dunktree_language_attributes');

/**
 * Clean up output of stylesheet <link> tags
 */
function dunktree_clean_style_tag($input) {
	preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches);
	if (empty($matches[2])) {
		return $input;
	}
	// Only display media if it is meaningful
	$media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
	return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
}
add_filter('style_loader_tag', 'dunktree_clean_style_tag');
/**
 * Clean up output of <script> tags
 */
function dunktree_clean_script_tag($input) {
	if ( is_admin() ) return $input;
	$input = str_replace("type='text/javascript' ", '', $input);
	return str_replace("'", '"', $input);
}
add_filter('script_loader_tag', 'dunktree_clean_script_tag');
/**
 * Add and remove body_class() classes
 */
function dunktree_body_class($classes) {
	// Add post/page slug if not present
	if (is_single() || is_page() && !is_front_page()) {
		if (!in_array(basename(get_permalink()), $classes)) {
			$classes[] = basename(get_permalink());
		}
	}
	// Remove unnecessary classes
	$home_id_class = 'page-id-' . get_option('page_on_front');
	$remove_classes = [
		'page-template-default',
		$home_id_class
	];
	$classes = array_diff($classes, $remove_classes);
	return $classes;
}
add_filter('body_class', 'dunktree_body_class');
/**
 * Wrap embedded media as suggested by Readability
 *
 * @link https://gist.github.com/965956
 * @link http://www.readability.com/publishers/guidelines#publisher
 */
function dunktree_embed_wrap($cache) {
	return '<div class="entry-content-asset">' . $cache . '</div>';
}
add_filter('embed_oembed_html', 'dunktree_embed_wrap');
/**
 * Remove unnecessary self-closing tags
 */
function dunktree_remove_self_closing_tags($input) {
	return str_replace(' />', '>', $input);
}
add_filter('get_avatar', 'dunktree_remove_self_closing_tags'); // <img />
add_filter('comment_id_fields', 'dunktree_remove_self_closing_tags'); // <input />
add_filter('post_thumbnail_html', 'dunktree_remove_self_closing_tags'); // <img />
/**
 * Don't return the default description in the RSS feed if it hasn't been changed
 */
function dunktree_remove_default_description($bloginfo) {
  $default_tagline = 'Just another WordPress site';
  return ($bloginfo === $default_tagline) ? '' : $bloginfo;
}
add_filter('get_bloginfo_rss', 'dunktree_remove_default_description');

/**
 * Add defer to scripts, let's keep them till last
 */
function dunktree_add_script_attributes($tag, $handle) {
	if ( is_admin() ) return $tag;
    return str_replace( ' src', ' defer="defer" src', $tag );
}
add_filter('script_loader_tag', 'dunktree_add_script_attributes', 10, 2);


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

