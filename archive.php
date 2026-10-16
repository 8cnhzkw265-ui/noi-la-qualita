<?php
/**
 * Fallback archivi generici (altri tipi di contenuto).
 *
 * @package Noi_La_Qualita
 */

get_header();
?>

<main id="content">
	<div class="page-head">
		<div class="container">
			<?php noi_la_qualita_breadcrumb(); ?>
			<h1><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<p>', '</p>' ); ?>
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
					<strong><?php esc_html_e( 'Nessun contenuto', 'noi-la-qualita' ); ?></strong>
					<?php esc_html_e( 'Non abbiamo trovato nulla con questi filtri.', 'noi-la-qualita' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
