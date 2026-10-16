<?php
/**
 * Configurazione del tema.
 *
 * @package Noi_La_Qualita
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'noi_la_qualita_setup' );
function noi_la_qualita_setup() {

	load_theme_textdomain( 'noi-la-qualita', NOI_LA_QUALITA_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'custom-logo', array(
		'height'      => 103,
		'width'       => 190,
		'flex-width'  => true,
		'flex-height' => true,
	) );

	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );

	register_nav_menus( array(
		'primary' => __( 'Menu principale', 'noi-la-qualita' ),
		'footer'  => __( 'Menu piè di pagina', 'noi-la-qualita' ),
	) );

	// Dimensioni utili alle card del catalogo.
	add_image_size( 'noi-card', 640, 640, false );
	add_image_size( 'noi-gallery', 1200, 1200, false );

	if ( ! isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 1200;
	}
}

/**
 * Schede prodotto senza commenti: catalogo, non blog.
 */
add_filter( 'register_post_type_args', 'noi_la_qualita_product_args', 10, 2 );
function noi_la_qualita_product_args( $args, $post_type ) {
	if ( 'prodotto' === $post_type ) {
		$args['supports'] = array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' );
	}
	return $args;
}
