<?php
/**
 * Template tags: breadcrumb, card prodotto, icone.
 *
 * @package Noi_La_Qualita
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Menu provvisorio finché non se ne definisce uno in Aspetto → Menu. */
function noi_la_qualita_fallback_menu() {
	echo '<ul class="menu">';
	printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/' ) ), esc_html__( 'Home', 'noi-la-qualita' ) );
	$archive = get_post_type_archive_link( 'prodotto' );
	if ( $archive ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $archive ), esc_html__( 'Catalogo', 'noi-la-qualita' ) );
	}
	printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/#tipi' ) ), esc_html__( 'Tipi di prodotto', 'noi-la-qualita' ) );
	printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/#contatti' ) ), esc_html__( 'Contatti', 'noi-la-qualita' ) );
	echo '</ul>';
}

/** Icona pin (inline SVG, zero dipendenze). */
function noi_la_qualita_icon_pin( $size = 15 ) {
	printf(
		'<svg width="%d" height="%d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-5.6-7-11a7 7 0 1 1 14 0c0 5.4-7 11-7 11z"/><circle cx="12" cy="10" r="2.6"/></svg>',
		(int) $size,
		(int) $size
	);
}

/** Icona freccia. */
function noi_la_qualita_icon_arrow( $size = 16 ) {
	printf(
		'<svg width="%d" height="%d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
		(int) $size,
		(int) $size
	);
}

/** Primo termine del tipo di prodotto di un post. */
function noi_la_qualita_get_type( $post_id = null ) {
	$terms = get_the_terms( $post_id ? $post_id : get_the_ID(), 'tipo_prodotto' );
	if ( is_array( $terms ) && ! empty( $terms ) ) {
		return $terms[0];
	}
	return null;
}

/** Etichetta regione di origine (fallback: "Italia"). */
function noi_la_qualita_get_region( $post_id = null ) {
	$terms = get_the_terms( $post_id ? $post_id : get_the_ID(), 'regione' );
	if ( is_array( $terms ) && ! empty( $terms ) ) {
		return $terms[0]->name;
	}
	return __( 'Italia', 'noi-la-qualita' );
}

/** Breadcrumb semantico (Home › Catalogo › Tipo › Prodotto). */
function noi_la_qualita_breadcrumb() {
	if ( is_front_page() ) {
		return;
	}
	echo '<ol class="breadcrumb" aria-label="' . esc_attr__( 'Percorso di navigazione', 'noi-la-qualita' ) . '">';
	printf(
		'<li><a href="%s">%s</a></li>',
		esc_url( home_url( '/' ) ),
		esc_html__( 'Home', 'noi-la-qualita' )
	);

	if ( is_singular( 'prodotto' ) ) {
		$archive = get_post_type_archive_link( 'prodotto' );
		if ( $archive ) {
			printf(
				'<li><a href="%s">%s</a></li>',
				esc_url( $archive ),
				esc_html__( 'Catalogo', 'noi-la-qualita' )
			);
		}
		$type = noi_la_qualita_get_type();
		if ( $type ) {
			printf(
				'<li><a href="%s">%s</a></li>',
				esc_url( get_term_link( $type ) ),
				esc_html( $type->name )
			);
		}
		printf( '<li aria-current="page">%s</li>', esc_html( get_the_title() ) );

	} elseif ( is_post_type_archive( 'prodotto' ) || get_query_var( 'prodotto' ) ) {
		printf( '<li aria-current="page">%s</li>', esc_html__( 'Catalogo', 'noi-la-qualita' ) );

	} elseif ( is_tax( 'tipo_prodotto' ) || is_tax( 'regione' ) ) {
		$archive = get_post_type_archive_link( 'prodotto' );
		if ( $archive ) {
			printf(
			 '<li><a href="%s">%s</a></li>',
				esc_url( $archive ),
				esc_html__( 'Catalogo', 'noi-la-qualita' )
			);
		}
		printf(
			'<li aria-current="page">%s</li>',
			esc_html( single_term_title( '', false ) )
		);

	} else {
		printf( '<li aria-current="page">%s</li>', esc_html( wp_get_document_title() ) );
	}

	echo '</ol>';
}

/** Immagine di copertina di un tipo di prodotto (term meta 'nqc_cover' con fallback di tema). */
function noi_la_qualita_term_cover( $term ) {
	$cover_id = get_term_meta( $term->term_id, 'nqc_cover', true );
	if ( $cover_id ) {
		$url = wp_get_attachment_image_url( (int) $cover_id, 'noi-card' );
		if ( $url ) {
			return $url;
		}
	}
	$defaults = array(
		'pasta'         => 'cat-pasta.jpg',
		'riso'          => 'cat-riso.jpg',
		'salumi'        => 'cat-salumi.jpg',
		'formaggi'      => 'cat-formaggi.jpg',
		'sottoli'       => 'cat-sottoli.jpg',
		'miele'         => 'cat-miele.jpg',
		'tonno'         => 'cat-tonno.jpg',
		'tartufi'       => 'cat-tartufi.jpg',
		'doppio-senso'  => 'cat-doppio-senso.jpg',
		'alcolici'      => 'cat-alcolici.jpg',
	);
	$file = isset( $defaults[ $term->slug ] ) ? $defaults[ $term->slug ] : 'placeholder.svg';
	return get_theme_file_uri( 'assets/img/' . $file );
}

/** Card prodotto (markup identico al concept HTML statico). */
function noi_la_qualita_product_card() {
	$id    = get_the_ID();
	$image = get_the_post_thumbnail( $id, 'noi-card' );
	$type  = noi_la_qualita_get_type( $id );
	$code  = get_post_meta( $id, '_nqc_code', true );
	if ( ! $image && $code ) {
		$catalog_image = 'catalog-' . sanitize_file_name( strtolower( $code ) ) . '.jpg';
		if ( file_exists( get_theme_file_path( 'assets/img/' . $catalog_image ) ) ) {
			$image = sprintf(
				'<img src="%s" alt="%s" loading="lazy" width="640" height="640">',
				esc_url( get_theme_file_uri( 'assets/img/' . $catalog_image ) ),
				esc_attr( get_the_title( $id ) )
			);
		}
	}
	?>
	<article <?php post_class( 'product-card' ); ?>>
		<a href="<?php the_permalink(); ?>">
			<span class="thumb">
				<?php if ( $image ) : ?>
					<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php else : ?>
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/placeholder.svg' ) ); ?>" alt="" loading="lazy" width="640" height="640">
				<?php endif; ?>
			</span>
			<span class="body">
				<?php if ( $type ) : ?>
					<span class="badge"><?php echo esc_html( $type->name ); ?></span>
				<?php endif; ?>
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<?php
				$format = get_post_meta( $id, '_nqc_format', true );
				$meta   = array_filter( array(
					$code ? sprintf( __( 'Cod. %s', 'noi-la-qualita' ), $code ) : '',
					$format,
				) );
				if ( $meta ) :
					?>
					<span class="sku"><?php echo esc_html( implode( ' · ', $meta ) ); ?></span>
				<?php endif; ?>
				<span class="region">
					<?php noi_la_qualita_icon_pin(); ?>
					<?php echo esc_html( noi_la_qualita_get_region( $id ) ); ?>
				</span>
				<span class="more"><?php esc_html_e( 'Scopri il prodotto', 'noi-la-qualita' ); ?> →</span>
			</span>
		</a>
	</article>
	<?php
}
