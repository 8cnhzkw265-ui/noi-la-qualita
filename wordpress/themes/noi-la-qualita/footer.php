<?php
/**
 * Footer del sito.
 *
 * @package Noi_La_Qualita
 */
?>
	<footer class="site-footer" id="contatti">
		<div class="container footer-top">
			<div>
				<img class="footer-logo" src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo-white.png' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy" width="210" height="113">
				<p><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
			</div>
			<div>
				<h4><?php esc_html_e( 'Navigazione', 'noi-la-qualita' ); ?></h4>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'menu',
						'depth'          => 1,
					) );
				} else {
					echo '<ul>';
					printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/' ) ), esc_html__( 'Home', 'noi-la-qualita' ) );
					$archive = get_post_type_archive_link( 'prodotto' );
					if ( $archive ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( $archive ), esc_html__( 'Catalogo prodotti', 'noi-la-qualita' ) );
					}
					printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/#tipi' ) ), esc_html__( 'Tipi di prodotto', 'noi-la-qualita' ) );
					echo '</ul>';
				}
				?>
			</div>
			<div>
				<h4><?php esc_html_e( 'Contatti', 'noi-la-qualita' ); ?></h4>
				<ul class="footer-contact">
					<li><?php noi_la_qualita_icon_pin( 16 ); ?><span><?php echo esc_html( noi_la_qualita_brand( 'noi_address' ) ); ?></span></li>
					<li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2z"/></svg>
						<span>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', noi_la_qualita_brand( 'noi_phone' ) ) ); ?>"><?php echo esc_html( noi_la_qualita_brand( 'noi_phone' ) ); ?></a>
							<?php if ( noi_la_qualita_brand( 'noi_phone2' ) ) : ?>
							 · <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', noi_la_qualita_brand( 'noi_phone2' ) ) ); ?>"><?php echo esc_html( noi_la_qualita_brand( 'noi_phone2' ) ); ?></a>
							<?php endif; ?>
						</span>
					</li>
					<li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>
						<span><a href="mailto:<?php echo esc_attr( noi_la_qualita_brand( 'noi_email' ) ); ?>"><?php echo esc_html( noi_la_qualita_brand( 'noi_email' ) ); ?></a></span>
					</li>
				</ul>
			</div>
		</div>
		<div class="container footer-bottom">
			<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?> — <?php esc_html_e( 'Tutti i diritti riservati.', 'noi-la-qualita' ); ?></span>
			<span><?php esc_html_e( 'Spedizioni in Italia ed estero · Catalogo prodotti', 'noi-la-qualita' ); ?></span>
		</div>
	</footer>

<?php wp_footer(); ?>
</body>
</html>
