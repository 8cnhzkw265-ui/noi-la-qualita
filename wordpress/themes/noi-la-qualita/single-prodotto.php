<?php
/**
 * Scheda prodotto: galleria, descrizione breve, regione di origine.
 *
 * @package Noi_La_Qualita
 */

get_header();

while ( have_posts() ) :
	the_post();

	$id       = get_the_ID();
	$gallery  = get_post_meta( $id, '_nqc_gallery', true );
	$gallery  = is_array( $gallery ) ? array_filter( array_map( 'absint', $gallery ) ) : array();
	$featured = get_post_thumbnail_id( $id );

	// L'immagine in evidenza è la prima della galleria (evita duplicati).
	if ( $featured ) {
		$gallery = array_values( array_unique( array_merge( array( $featured ), $gallery ) ) );
	}

	$code   = get_post_meta( $id, '_nqc_code', true );
	$format = get_post_meta( $id, '_nqc_format', true );
	$type   = noi_la_qualita_get_type( $id );
	$region = noi_la_qualita_get_region( $id );
	?>

	<div class="page-head">
		<div class="container">
			<?php noi_la_qualita_breadcrumb(); ?>
			<p>
				<?php
				$parts = array();
				if ( $type ) {
					$parts[] = esc_html( $type->name );
				}
				if ( $code ) {
					$parts[] = sprintf( __( 'cod. %s', 'noi-la-qualita' ), $code );
				}
				if ( $format ) {
					$parts[] = esc_html( $format );
				}
				echo esc_html( implode( ' · ', $parts ) );
				?>
			</p>
		</div>
	</div>

	<section class="section">
		<div class="container">
			<div class="product-layout">

				<!-- Galleria -->
				<div class="gallery" id="gallery">
					<?php if ( $gallery ) : ?>
						<?php
						$main_id  = $gallery[0];
						$main_url = wp_get_attachment_image_url( $main_id, 'noi-gallery' );
						$main_alt = get_post_meta( $main_id, '_wp_attachment_image_alt', true );
						if ( ! $main_alt ) {
							$main_alt = get_the_title();
						}
						?>
						<div class="gallery-main" id="gallery-main" role="button" tabindex="0" aria-label="<?php esc_attr_e( "Ingrandisci l'immagine del prodotto", 'noi-la-qualita' ); ?>">
							<img id="gallery-main-img" src="<?php echo esc_url( $main_url ); ?>" alt="<?php echo esc_attr( $main_alt ); ?>" width="1200" height="1200">
							<span class="zoom-hint">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3M11 8v6M8 11h6"/></svg>
								<?php esc_html_e( 'Clicca per ingrandire', 'noi-la-qualita' ); ?>
							</span>
						</div>
						<?php if ( count( $gallery ) > 1 ) : ?>
							<div class="gallery-thumbs" role="tablist" aria-label="<?php esc_attr_e( 'Immagini del prodotto', 'noi-la-qualita' ); ?>">
								<?php foreach ( $gallery as $i => $img_id ) : ?>
									<?php
									$full  = wp_get_attachment_image_url( $img_id, 'noi-gallery' );
									$thumb = wp_get_attachment_image_url( $img_id, 'thumbnail' );
									$alt   = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
									if ( ! $alt ) {
										$alt = get_the_title();
									}
									?>
									<button type="button" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
										data-full="<?php echo esc_url( $full ); ?>" data-alt="<?php echo esc_attr( $alt ); ?>">
										<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" width="120" height="120">
									</button>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					<?php else : ?>
						<div class="gallery-main" style="cursor: default;">
							<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/placeholder.svg' ) ); ?>" alt="" width="640" height="640">
						</div>
					<?php endif; ?>
				</div>

				<!-- Informazioni -->
				<div class="product-info">
					<?php if ( $type ) : ?>
						<span class="badge"><?php echo esc_html( $type->name ); ?></span>
					<?php endif; ?>

					<h1><?php the_title(); ?></h1>

					<span class="sku">
						<?php
						$sku = array();
						if ( $code ) {
							$sku[] = sprintf( __( 'Cod. %s', 'noi-la-qualita' ), $code );
						}
						if ( $format ) {
							$sku[] = $format;
						}
						echo esc_html( implode( ' · ', $sku ) );
						?>
					</span>

					<div class="product-desc">
						<?php
						if ( has_excerpt() ) {
							echo wp_kses_post( wpautop( get_the_excerpt() ) );
						} else {
							the_content();
						}
						?>
					</div>

					<div class="region-callout">
						<span class="pin" aria-hidden="true"><?php noi_la_qualita_icon_pin( 20 ); ?></span>
						<span>
							<span class="label"><?php esc_html_e( 'Regione di origine', 'noi-la-qualita' ); ?></span>
							<span class="value"><?php echo esc_html( $region ); ?></span>
						</span>
					</div>

					<?php if ( $format || $code ) : ?>
						<dl class="spec-list">
							<?php if ( $format ) : ?>
								<div><dt><?php esc_html_e( 'Formato', 'noi-la-qualita' ); ?></dt><dd><?php echo esc_html( $format ); ?></dd></div>
							<?php endif; ?>
							<?php if ( $code ) : ?>
								<div><dt><?php esc_html_e( 'Codice', 'noi-la-qualita' ); ?></dt><dd><?php echo esc_html( $code ); ?></dd></div>
							<?php endif; ?>
							<?php if ( $type ) : ?>
								<div><dt><?php esc_html_e( 'Tipo', 'noi-la-qualita' ); ?></dt><dd><?php echo esc_html( $type->name ); ?></dd></div>
							<?php endif; ?>
							<div>
								<dt><?php esc_html_e( 'Origine', 'noi-la-qualita' ); ?></dt>
								<dd><?php echo esc_html( $region ); ?></dd>
							</div>
						</dl>
					<?php endif; ?>

					<div class="product-actions">
						<a class="btn btn-primary" href="mailto:<?php echo esc_attr( noi_la_qualita_brand( 'noi_email' ) ); ?>?subject=<?php echo rawurlencode( sprintf( __( 'Informazioni su %s', 'noi-la-qualita' ), get_the_title() ) ); ?>">
							<?php esc_html_e( 'Richiedi informazioni', 'noi-la-qualita' ); ?>
						</a>
						<?php if ( $type ) : ?>
							<a class="btn btn-outline" href="<?php echo esc_url( get_term_link( $type ) ); ?>">
								<?php
								// translators: %s: tipo di prodotto.
								printf( esc_html__( 'Altri prodotti %s', 'noi-la-qualita' ), esc_html( $type->name ) );
								?>
							</a>
						<?php endif; ?>
					</div>
				</div>

			</div>
		</div>
	</section>

	<?php
	// Prodotti correlati (stesso tipo, escluso il corrente).
	$related_args = array(
		'post_type'           => 'prodotto',
		'posts_per_page'      => 3,
		'post__not_in'        => array( $id ),
		'orderby'             => 'rand',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	if ( $type ) {
		$related_args['tax_query'] = array(
			array(
				'taxonomy'         => 'tipo_prodotto',
				'field'            => 'term_id',
				'terms'            => $type->term_id,
				'include_children' => false,
			),
		);
	}
	$related = new WP_Query( $related_args );
	?>

	<?php if ( $related->have_posts() ) : ?>
		<section class="section alt">
			<div class="container">
				<div class="section-head">
					<div>
						<span class="eyebrow"><?php esc_html_e( 'Continua a sfogliare', 'noi-la-qualita' ); ?></span>
						<h2><?php esc_html_e( 'Altri prodotti dal catalogo', 'noi-la-qualita' ); ?></h2>
					</div>
					<?php if ( get_post_type_archive_link( 'prodotto' ) ) : ?>
						<a class="link-all" href="<?php echo esc_url( get_post_type_archive_link( 'prodotto' ) ); ?>"><?php esc_html_e( 'Tutto il catalogo', 'noi-la-qualita' ); ?><?php noi_la_qualita_icon_arrow(); ?></a>
					<?php endif; ?>
				</div>
				<div class="product-grid">
					<?php
					while ( $related->have_posts() ) :
						$related->the_post();
						noi_la_qualita_product_card();
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- Lightbox galleria -->
	<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Galleria prodotto', 'noi-la-qualita' ); ?>">
		<button type="button" class="lightbox-btn lightbox-close" aria-label="<?php esc_attr_e( 'Chiudi', 'noi-la-qualita' ); ?>">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>
		<button type="button" class="lightbox-btn lightbox-prev" aria-label="<?php esc_attr_e( 'Immagine precedente', 'noi-la-qualita' ); ?>">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
		</button>
		<img id="lightbox-img" src="" alt="">
		<button type="button" class="lightbox-btn lightbox-next" aria-label="<?php esc_attr_e( 'Immagine successiva', 'noi-la-qualita' ); ?>">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
		</button>
		<p class="lightbox-caption" id="lightbox-caption"></p>
	</div>

<?php
endwhile;

get_footer();
