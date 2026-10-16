<?php
/**
 * Parte condivisa: testata catalogo + filtri + griglia + paginazione.
 * Usato da archive-prodotto.php, taxonomy-tipo_prodotto.php,
 * taxonomy-regione.php. I filtri funzionano anche senza JavaScript.
 *
 * @package Noi_La_Qualita
 *
 * @var array $args['heading']   Titolo opzionale (default: titolo archivio/tassonomia).
 * @var array $args['subhead']   Sottotitolo opzionale.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading   = ! empty( $args['heading'] ) ? $args['heading'] : '';
$subhead   = ! empty( $args['subhead'] ) ? $args['subhead'] : '';
$current_t = is_tax( 'tipo_prodotto' ) ? get_queried_object() : null;
$cur_tipo  = $current_t ? $current_t->slug : noi_catalog_filter( 'tipo' );
$cur_reg   = is_tax( 'regione' ) ? get_queried_object()->slug : noi_catalog_filter( 'regione' );
$cur_q     = noi_catalog_search();

if ( ! $heading ) {
	$heading = is_tax() ? single_term_title( '', false ) : __( 'Catalogo prodotti', 'noi-la-qualita' );
}
if ( ! $subhead ) {
	$subhead = is_tax() && term_description()
		? wp_kses_post( term_description() )
		: __( "Tutte le referenze del catalogo in un'unica pagina. Usa i filtri per tipo e per regione di origine, oppure cerca per nome o codice.", 'noi-la-qualita' );
}

$archive_url = get_post_type_archive_link( 'prodotto' );
?>

<div class="page-head">
	<div class="container">
		<?php noi_la_qualita_breadcrumb(); ?>
		<h1><?php echo esc_html( $heading ); ?></h1>
		<p><?php echo wp_kses_post( $subhead ); ?></p>
		<?php if ( is_tax( 'tipo_prodotto' ) && $current_t ) : ?>
			<p class="page-head-back">
				<a href="<?php echo esc_url( $archive_url ); ?>">← <?php esc_html_e( 'Torna al catalogo completo', 'noi-la-qualita' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</div>

<section class="section">
	<div class="container">

		<?php
		// Filtri per tipo (link alle tassonomie: URL canonici, funzionano senza JS).
		$types = get_terms( array(
			'taxonomy'   => 'tipo_prodotto',
			'hide_empty' => false,
		) );
		$regions = get_terms( array(
			'taxonomy'   => 'regione',
			'hide_empty' => false,
		) );
		?>

		<div class="filters">
			<div class="filter-pills" id="type-filters">
				<?php if ( $archive_url ) : ?>
					<a class="filter-pill<?php echo $cur_tipo ? '' : ' is-active'; ?>"
						href="<?php echo esc_url( $archive_url ); ?>"
						<?php echo $cur_tipo ? '' : 'aria-current="page"'; ?>>
						<?php esc_html_e( 'Tutti', 'noi-la-qualita' ); ?>
					</a>
				<?php endif; ?>
				<?php
				if ( ! is_wp_error( $types ) && $types ) :
					foreach ( $types as $t ) :
						$link = get_term_link( $t );
						if ( is_wp_error( $link ) ) {
							continue;
						}
						?>
						<a class="filter-pill<?php echo ( $cur_tipo === $t->slug ) ? ' is-active' : ''; ?>"
							href="<?php echo esc_url( $link ); ?>"
							<?php echo ( $cur_tipo === $t->slug ) ? 'aria-current="page"'; ?>>
							<?php echo esc_html( $t->name ); ?> <span class="count"><?php echo esc_html( $t->count ); ?></span>
						</a>
						<?php
					endforeach;
				endif;
				?>
			</div>

			<div class="filter-spacer"></div>

			<?php if ( ! is_wp_error( $regions ) && $regions ) : ?>
				<form class="filter-region" id="region-form" method="get"
					action="<?php echo esc_url( is_tax( 'tipo_prodotto' ) ? get_term_link( $current_t ) : $archive_url ); ?>">
					<?php if ( $cur_q ) : ?>
						<input type="hidden" name="q" value="<?php echo esc_attr( $cur_q ); ?>">
					<?php endif; ?>
					<label class="visually-hidden" for="region-filter"><?php esc_html_e( 'Filtra per regione', 'noi-la-qualita' ); ?></label>
					<select id="region-filter" name="regione">
						<option value=""><?php esc_html_e( 'Tutte le regioni', 'noi-la-qualita' ); ?></option>
						<?php foreach ( $regions as $r ) : ?>
							<option value="<?php echo esc_attr( $r->slug ); ?>" <?php selected( $cur_reg, $r->slug ); ?>>
								<?php echo esc_html( $r->name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</form>
			<?php endif; ?>

			<form class="filter-search" method="get"
				action="<?php echo esc_url( is_tax( 'tipo_prodotto' ) ? get_term_link( $current_t ) : $archive_url ); ?>">
				<?php if ( $cur_reg ) : ?>
					<input type="hidden" name="regione" value="<?php echo esc_attr( $cur_reg ); ?>">
				<?php endif; ?>
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
				<label class="visually-hidden" for="search-input"><?php esc_html_e( 'Cerca un prodotto', 'noi-la-qualita' ); ?></label>
				<input type="search" id="search-input" name="q" value="<?php echo esc_attr( $cur_q ); ?>"
					placeholder="<?php esc_attr_e( 'Cerca per nome o codice…', 'noi-la-qualita' ); ?>" autocomplete="off">
			</form>
		</div>

		<?php
		/* phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound */
		global $wp_query;
		$total = $wp_query ? (int) $wp_query->found_posts : 0;
		/* phpcs:enable */
		?>
		<p class="result-line" aria-live="polite">
			<strong><?php echo esc_html( $total ); ?></strong>
			<?php echo esc_html( _n( 'prodotto trovato', 'prodotti trovati', max( 1, $total ), 'noi-la-qualita' ) ); ?>
		</p>

		<?php if ( have_posts() ) : ?>
			<div class="product-grid" id="product-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					noi_la_qualita_product_card();
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination( array(
				'mid_size'  => 2,
				'prev_text' => __( '← Precedente', 'noi-la-qualita' ),
				'next_text' => __( 'Successiva →', 'noi-la-qualita' ),
			) );
			?>

		<?php else : ?>
			<div class="empty-state show">
				<strong><?php esc_html_e( 'Nessun prodotto corrisponde ai filtri', 'noi-la-qualita' ); ?></strong>
				<?php esc_html_e( 'Prova a togliere un filtro o a cercare un altro termine.', 'noi-la-qualita' ); ?>
			</div>
		<?php endif; ?>

	</div>
</section>

