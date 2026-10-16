<?php
/**
 * Pagine generiche.
 *
 * @package Noi_La_Qualita
 */

get_header();
?>

<main id="content">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<div class="page-head">
			<div class="container">
				<?php noi_la_qualita_breadcrumb(); ?>
				<h1><?php the_title(); ?></h1>
			</div>
		</div>

		<section class="section">
			<div class="container entry-content">
				<?php the_content(); ?>
			</div>
		</section>
		<?php
	endwhile;
	?>
</main>

<?php get_footer(); ?>
