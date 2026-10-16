<?php
/**
 * Pagina 404.
 *
 * @package Noi_La_Qualita
 */

get_header();
?>

<main id="content">
	<div class="page-head">
		<div class="container">
			<ol class="breadcrumb" aria-label="<?php esc_attr_e( 'Percorso di navigazione', 'noi-la-qualita' ); ?>">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'noi-la-qualita' ); ?></a></li>
				<li aria-current="page">404</li>
			</ol>
			<h1><?php esc_html_e( 'Pagina non trovata', 'noi-la-qualita' ); ?></h1>
			<p><?php esc_html_e( 'La pagina che cerchi non esiste più. Da qui puoi tornare al catalogo o cercare un prodotto.', 'noi-la-qualita' ); ?></p>
		</div>
	</div>

	<section class="section">
		<div class="container">
			<div class="empty-state show">
				<strong><?php esc_html_e( 'Errore 404', 'noi-la-qualita' ); ?></strong>
				<p><?php get_search_form(); ?></p>
				<p>
					<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Torna alla home', 'noi-la-qualita' ); ?></a>
					<?php if ( get_post_type_archive_link( 'prodotto' ) ) : ?>
						<a class="btn btn-outline" href="<?php echo esc_url( get_post_type_archive_link( 'prodotto' ) ); ?>"><?php esc_html_e( 'Apri il catalogo', 'noi-la-qualita' ); ?></a>
					<?php endif; ?>
				</p>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
