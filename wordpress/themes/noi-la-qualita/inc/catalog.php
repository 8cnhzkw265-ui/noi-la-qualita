<?php
/**
 * Logica del catalogo: filtri server-side (tipo, regione, ricerca).
 * Funziona senza JavaScript; l'URL resta condivisibile e indicizzabile.
 *
 * @package Noi_La_Qualita
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Legge un parametro di filtro dalla query string. */
function noi_catalog_filter( $key ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $_GET[ $key ] ) ) {
		return '';
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return sanitize_title( wp_unslash( $_GET[ $key ] ) );
}

/** Ricerca testuale libera (q). */
function noi_catalog_search() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $_GET['q'] ) ) {
		return '';
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return sanitize_text_field( wp_unslash( $_GET['q'] ) );
}

add_action( 'pre_get_posts', 'noi_catalog_pre_get_posts' );
function noi_catalog_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$is_archive = $query->is_post_type_archive( 'prodotto' );
	$is_tax     = $query->is_tax( array( 'tipo_prodotto', 'regione' ) );

	if ( ! $is_archive && ! $is_tax ) {
		return;
	}

	// Ricerca per nome/codice (?q=).
	$s = noi_catalog_search();
	if ( $s ) {
		$query->set( 's', $s );
	}

	$tax_query = array();

	if ( $is_archive ) {
		$tipo    = noi_catalog_filter( 'tipo' );
		$regione = noi_catalog_filter( 'regione' );

		if ( $tipo ) {
			$tax_query[] = array(
				'taxonomy'         => 'tipo_prodotto',
				'field'            => 'slug',
				'terms'            => $tipo,
				'include_children' => false,
			);
		}
		if ( $regione ) {
			$tax_query[] = array(
				'taxonomy' => 'regione',
				'field'    => 'slug',
				'terms'    => $regione,
			);
		}
	} else {
		// Pagina di un tipo: la regione opzionale si somma al termine corrente.
		$regione = noi_catalog_filter( 'regione' );
		if ( $regione ) {
			$tax_query[] = array(
				'taxonomy' => 'regione',
				'field'    => 'slug',
				'terms'    => $regione,
			);
		}
	}

	if ( $tax_query ) {
		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}
		$query->set( 'tax_query', $tax_query );
	}

	$query->set( 'orderby', 'menu_order date' );
	$query->set( 'order', 'ASC' );
}
