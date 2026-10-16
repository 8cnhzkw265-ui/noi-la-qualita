<?php
/**
 * Tema principale "Noi la Qualità".
 *
 * @package Noi_La_Qualita
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NOI_LA_QUALITA_VERSION', '1.0.0' );
define( 'NOI_LA_QUALITA_DIR', get_template_directory() );
define( 'NOI_LA_QUALITA_URI', get_template_directory_uri() );

require NOI_LA_QUALITA_DIR . '/inc/setup.php';
require NOI_LA_QUALITA_DIR . '/inc/enqueue.php';
require NOI_LA_QUALITA_DIR . '/inc/customizer.php';
require NOI_LA_QUALITA_DIR . '/inc/template-tags.php';
require NOI_LA_QUALITA_DIR . '/inc/catalog.php';
