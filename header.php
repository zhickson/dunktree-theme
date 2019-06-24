<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Dunktree
 */

?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'dunktree' ); ?></a>

	<header id="masthead" class="site-header wrapper">
		<div class="site-branding">
			<a href="/" rel="home"><img src="<?php echo get_template_directory_uri(); ?>/dist/images/Dunktree-horizontal-white-rgb.svg" alt="Dunktree logo"></a>
		</div><!-- .site-branding -->
	</header><!-- #masthead -->

	<div id="content" class="site-content wrapper">
