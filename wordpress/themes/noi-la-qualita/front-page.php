<?php
/**
 * Homepage: hero, tipi di prodotto, prodotti in evidenza, brand, valori.
 *
 * @package Noi_La_Qualita
 */

get_header();
?>

<main id="content">

	<!-- Hero -->
	<section class="hero">
		<img class="hero-media" src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero-cellar.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Bottega italiana con salumi, formaggi e conserve', 'noi-la-qualita' ); ?>" fetchpriority="high">
		<div class="container">
			<div class="hero-inner">
				<span class="eyebrow on-dark"><?php esc_html_e( 'Catalogo prodotti', 'noi-la-qualita' ); ?></span>
				<h1><?php esc_html_e( 'Le specialità italiane, selezionate con cura.', 'noi-la-qualita' ); ?></h1>
				<p class="lead"><?php esc_html_e( 'Pasta, salumi, formaggi, conserve, miele, tonno, tartufi e distillati: esplora il nostro catalogo digitale e scopri origine e qualità di ogni referenza.', 'noi-la-qualita' ); ?></p>
				<div class="hero-actions">
					<?php
					$archive = get_post_type_archive_link( 'prodotto' );
					if ( $archive ) :
						?>
						<a class="btn btn-primary" href="<?php echo esc_url( $archive ); ?>"><?php esc_html_e( 'Sfoglia il catalogo', 'noi-la-qualita' ); ?></a>
					<?php endif; ?>
					<a class="btn btn-ghost" href="#tipi"><?php esc_html_e( 'Vedi i tipi di prodotto', 'noi-la-qualita' ); ?></a>
				</div>
			</div>
			<?php
			$n_types    = wp_count_terms( 'tipo_prodotto' );
			$n_products = wp_count_posts( 'prodotto' );
			?>
			<div class="hero-stats">
				<div class="stat"><strong><?php echo esc_html( is_numeric( $n_types ) ? $n_types : '10' ); ?></strong><span><?php esc_html_e( 'tipi di prodotto', 'noi-la-qualita' ); ?></span></div>
				<div class="stat"><strong><?php echo esc_html( is_numeric( $n_products ) ? $n_products : '0' ); ?></strong><span><?php esc_html_e( 'prodotti a catalogo', 'noi-la-qualita' ); ?></span></div>
				<div class="stat"><strong>IT · EN</strong><span><?php esc_html_e( 'struttura multilingue', 'noi-la-qualita' ); ?></span></div>
			</div>
		</div>
	</section>

	<!-- Tipi di prodotto -->
	<section class="section" id="tipi">
		<div class="container">
			<div class="section-head">
				<div>
					<span class="eyebrow"><?php esc_html_e( 'Navigazione rapida', 'noi-la-qualita' ); ?></span>
					<h2><?php esc_html_e( 'Trova tutto per tipo di prodotto', 'noi-la-qualita' ); ?></h2>
					<p><?php esc_html_e( 'Le famiglie di prodotto del catalogo stampato: un clic e sei nella sezione giusta.', 'noi-la-qualita' ); ?></p>
				</div>
				<?php if ( get_post_type_archive_link( 'prodotto' ) ) : ?>
					<a class="link-all" href="<?php echo esc_url( get_post_type_archive_link( 'prodotto' ) ); ?>"><?php esc_html_e( 'Tutto il catalogo', 'noi-la-qualita' ); ?><?php noi_la_qualita_icon_arrow(); ?></a>
				<?php endif; ?>
			</div>

			<div class="cat-grid">
				<?php
				$types = get_terms( array(
					'taxonomy'   => 'tipo_prodotto',
					'hide_empty' => false,
				) );
				if ( ! is_wp_error( $types ) && $types ) :
					foreach ( $types as $term ) :
						$cover = noi_la_qualita_term_cover( $term );
						?>
						<a class="cat-card" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
							<span class="thumb"><img src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" loading="lazy" width="900" height="900"></span>
							<span class="label"><?php echo esc_html( $term->name ); ?><?php noi_la_qualita_icon_arrow(); ?></span>
						</a>
						<?php
					endforeach;
				endif;
				?>
			</div>
		</div>
	</section>

	<!-- Prodotti in evidenza -->
	<section class="section alt" id="prodotti">
		<div class="container">
			<div class="section-head">
				<div>
					<span class="eyebrow"><?php esc_html_e( 'Dal nostro catalogo', 'noi-la-qualita' ); ?></span>
					<h2><?php esc_html_e( 'Prodotti in evidenza', 'noi-la-qualita' ); ?></h2>
					<p><?php esc_html_e( 'Gli ultimi prodotti inseriti: ogni scheda riporta descrizione breve, codice e regione di origine.', 'noi-la-qualita' ); ?></p>
				</div>
				<?php if ( get_post_type_archive_link( 'prodotto' ) ) : ?>
					<a class="link-all" href="<?php echo esc_url( get_post_type_archive_link( 'prodotto' ) ); ?>"><?php esc_html_e( 'Vedi tutti i prodotti', 'noi-la-qualita' ); ?><?php noi_la_qualita_icon_arrow(); ?></a>
				<?php endif; ?>
			</div>

			<?php
			$featured = new WP_Query( array(
				'post_type'      => 'prodotto',
				'posts_per_page' => 5,
				'orderby'        => 'menu_order date',
				'order'          => 'ASC',
			) );
			if ( $featured->have_posts() ) :
				echo '<div class="product-grid">';
				while ( $featured->have_posts() ) :
					$featured->the_post();
					noi_la_qualita_product_card();
				endwhile;
				echo '</div>';
				wp_reset_postdata();
			else :
				?>
				<div class="empty-state show">
					<strong><?php esc_html_e( 'Nessun prodotto ancora', 'noi-la-qualita' ); ?></strong>
					<?php esc_html_e( 'Aggiungi prodotti dal menu Prodotti → Aggiungi nuovo, oppure importa i 5 prodotti demo da Strumenti → Importa prodotti demo.', 'noi-la-qualita' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- Il brand -->
	<section class="section" id="brand">
		<div class="container">
			<div class="story">
				<div>
					<span class="eyebrow"><?php esc_html_e( 'Il nostro brand', 'noi-la-qualita' ); ?></span>
					<h2>Golosaqualità — High Quality Food</h2>
					<div class="logo-strip">
						<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo.png' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy" width="170" height="92">
						<strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?></strong>
					</div>
					<p><?php esc_html_e( 'Selezioniamo specialità alimentari da tutta Italia — dalla pasta trafilata al bronzo ai salumi, dai formaggi di pecora alle conserve di pomodoro — e le portiamo sulle tavole di Italia ed estero.', 'noi-la-qualita' ); ?></p>
					<ul>
						<li><?php esc_html_e( 'Selezione di produttori italiani e filiera controllata', 'noi-la-qualita' ); ?></li>
						<li><?php esc_html_e( 'Origine dichiarata su ogni prodotto', 'noi-la-qualita' ); ?></li>
						<li><?php esc_html_e( 'Spedizioni in Italia ed estero, su richiesta', 'noi-la-qualita' ); ?></li>
					</ul>
					<?php if ( get_post_type_archive_link( 'prodotto' ) ) : ?>
						<a class="btn btn-outline" href="<?php echo esc_url( get_post_type_archive_link( 'prodotto' ) ); ?>"><?php esc_html_e( 'Esplora il catalogo', 'noi-la-qualita' ); ?></a>
					<?php endif; ?>
				</div>
				<div class="story-media">
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/spaghettoni-4.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Piatto della tradizione italiana', 'noi-la-qualita' ); ?>" loading="lazy" width="439" height="1200">
				</div>
			</div>
		</div>
	</section>

	<!-- Valori -->
	<section class="section alt">
		<div class="container">
			<div class="section-head">
				<div>
					<span class="eyebrow"><?php esc_html_e( 'Perché sceglierci', 'noi-la-qualita' ); ?></span>
					<h2><?php esc_html_e( 'Qualità che si vede, origine che si legge', 'noi-la-qualita' ); ?></h2>
				</div>
			</div>
			<div class="values-grid">
				<div class="value-card" tabindex="0">
					<div class="ico"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></div>
					<h3><?php esc_html_e( 'Selezione controllata', 'noi-la-qualita' ); ?></h3>
					<p><?php esc_html_e( 'Ogni referenza del catalogo è scelta insieme a produttori italiani con comprovata tradizione artigianale.', 'noi-la-qualita' ); ?></p>
				</div>
				<div class="value-card" tabindex="0">
					<div class="ico"><?php noi_la_qualita_icon_pin( 22 ); ?></div>
					<h3><?php esc_html_e( 'Regione di origine', 'noi-la-qualita' ); ?></h3>
					<p><?php esc_html_e( 'Scheda prodotto con regione di provenienza sempre in evidenza: sai esattamente da dove arriva ciò che porti a tavola.', 'noi-la-qualita' ); ?></p>
				</div>
				<div class="value-card" tabindex="0">
					<div class="ico"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="15" height="10" rx="2"/><path d="M17 10h3l2 3v4h-5"/><circle cx="6.5" cy="18.5" r="1.8"/><circle cx="17.5" cy="18.5" r="1.8"/></svg></div>
					<h3><?php esc_html_e( 'Spedizioni Italia ed estero', 'noi-la-qualita' ); ?></h3>
					<p><?php esc_html_e( 'Tempi e costi di consegna definiti in base alle esigenze del cliente.', 'noi-la-qualita' ); ?></p>
				</div>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>

