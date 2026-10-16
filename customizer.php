<?php
/**
 * Personalizzazione: contatti e colore d'accento (default = palette esatta del brand).
 *
 * @package Noi_La_Qualita
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_register', 'noi_la_qualita_customize' );
function noi_la_qualita_customize( $wp_customize ) {

	$wp_customize->add_section( 'noi_brand', array(
		'title'    => __( 'Dati del brand', 'noi-la-qualita' ),
		'priority' => 30,
	) );

	$fields = array(
		'noi_phone'   => array( __( 'Telefono', 'noi-la-qualita' ), '+39 06 66995093' ),
		'noi_phone2'  => array( __( 'Telefono 2', 'noi-la-qualita' ), '+39 379 1413514' ),
		'noi_email'   => array( __( 'E-mail', 'noi-la-qualita' ), 'info@noilaqualita.it' ),
		'noi_address' => array( __( 'Indirizzo', 'noi-la-qualita' ), 'Viale C. Sabatini, 161 — 00144 Roma, Italia' ),
		'noi_site'    => array( __( 'Sito web', 'noi-la-qualita' ), 'www.noilaqualita.it' ),
	);

	foreach ( $fields as $id => $def ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $def[1],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $def[0],
			'section' => 'noi_brand',
			'type'    => 'text',
		) );
	}

	// Colore d'accento — default identico alla palette stampata.
	$wp_customize->add_setting( 'noi_accent', array(
		'default'           => '#A32529',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'noi_accent', array(
		'label'   => __( "Colore d'accento", 'noi-la-qualita' ),
		'section' => 'noi_brand',
	) ) );
}

/**
 * Istanza helper dei valori del brand.
 */
function noi_la_qualita_brand( $key ) {
	$defaults = array(
		'noi_phone'   => '+39 06 66995093',
		'noi_phone2'  => '+39 379 1413514',
		'noi_email'   => 'info@noilaqualita.it',
		'noi_address' => 'Viale C. Sabatini, 161 — 00144 Roma, Italia',
		'noi_site'    => 'www.noilaqualita.it',
		'noi_accent'  => '#A32529',
	);
	$value = get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
	return $value;
}
