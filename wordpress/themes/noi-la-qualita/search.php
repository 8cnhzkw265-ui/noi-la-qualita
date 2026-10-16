<?php
/**
 * Risultati di ricerca.
 *
 * @package Noi_La_Qualita
 */

get_header();
?>

<main id="content">
	<div class="page-head">
		<div class="container">
			<?php noi_la_qualita_breadcrumb(); ?>
			<h1>
				<?php
				// translators: %s: termine cercato.
				printf( esc_html__( 'Risultati per: %s', 'noi-la-qualita' ), '<em>' . esc_html( get_search_query() ) . '</em>' );
				?>
			</h1>
			<p>
				<?php
				// translators: %d: numero risultati.
				printf( esc_html( _n( '%d risultato trovato', '%d risultati trovati', (int) $GLOBALS['wp_query']->found_posts, 'noi-la-qualita' ) ), (int) $GLOBALS['wp_query']->found_posts );
				?>
			</p>
		</div>
	</div>

	<section class="section">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<div class="product-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						if ( 'prodotto' === get_post_type() ) {
							noi_la_qualita_product_card();
						} else {
							?>
							<article <?php post_class( 'product-card' ); ?>>
								<a href="<?php the_permalink(); ?>">
									<span class="body">
										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
										<span class="more"><?php esc_html_e( 'Leggi', 'noi-la-qualita' ); ?> →</span>
									</span>
								</a>
							</article>
							<?php
						}
					endwhile;
					?>
				</div>
				<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
			<?php else : ?>
				<div class="empty-state show">
					<strong><?php esc_html_e( 'Nessun risultato', 'noi-la-qualita' ); ?></strong>
					<?php esc_html_e( 'Prova con un altro termine o sfoglia il catalogo completo.', 'noi-la-qualita' ); ?>
					<p><a class="btn btn-primary" href="<?php echo esc_url( get_post_type_archive_link( 'prodotto' ) ); ?>"><?php esc_html_e( 'Vai al catalogo', 'noi-la-qualita' ); ?></a></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
