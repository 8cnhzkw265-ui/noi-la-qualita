<?php
/**
 * Header del sito.
 *
 * @package Noi_La_Qualita
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Vai al contenuto', 'noi-la-qualita' ); ?></a>

<header class="site-header">
	<div class="container header-inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Noi, la qualità — Home', 'noi-la-qualita' ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo.png' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="190" height="103">
			<?php endif; ?>
		</a>

		<button class="nav-toggle" aria-expanded="false" aria-controls="main-nav" aria-label="<?php esc_attr_e( 'Apri il menu', 'noi-la-qualita' ); ?>">
			<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#24416B" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
		</button>

		<nav class="main-nav" id="main-nav" aria-label="<?php esc_attr_e( 'Menu principale', 'noi-la-qualita' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'menu',
				'depth'          => 2,
				'fallback_cb'    => 'noi_la_qualita_fallback_menu',
			) );
			?>
			<a class="btn btn-primary nav-cta" href="<?php echo esc_url( get_post_type_archive_link( 'prodotto' ) ? get_post_type_archive_link( 'prodotto' ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'Sfoglia i prodotti', 'noi-la-qualita' ); ?></a>
		</nav>
	</div>
</header>

