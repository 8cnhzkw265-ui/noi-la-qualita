<?php
/**
 * Enqueue di stili e script (nessuna dipendenza esterna).
 *
 * @package Noi_La_Qualita
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'noi_la_qualita_assets' );
function noi_la_qualita_assets() {

	wp_enqueue_style(
		'noi-la-qualita',
		NOI_LA_QUALITA_URI . '/assets/css/style.css',
		array(),
		NOI_LA_QUALITA_VERSION
	);

	wp_enqueue_script(
		'noi-la-qualita',
		NOI_LA_QUALITA_URI . '/assets/js/main.js',
		array(),
		NOI_LA_QUALITA_VERSION,
		true
	);

	// Preload dell'hero sulla homepage per il LCP (< 3s in 4G).
	if ( is_front_page() && file_exists( NOI_LA_QUALITA_DIR . '/assets/img/hero-cellar.jpg' ) ) {
		add_action( 'wp_head', 'noi_la_qualita_preload_hero', 1 );
	}
}

function noi_la_qualita_preload_hero() {
	printf(
		'<link rel="preload" as="image" href="%s">' . "\n",
		esc_url( NOI_LA_QUALITA_URI . '/assets/img/hero-cellar.jpg' )
	);
}
