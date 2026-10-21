<?php
/**
 * Title banner with breadcrumbs for pages, archives, search and the shop.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

if ( ! otb_show_page_header() ) {
	return;
}

$otb_style       = otb_option( 'page_header_style' );
$otb_title       = '';
$otb_description = '';
$otb_eyebrow     = '';
$otb_bg          = 0;

if ( otb_is_woocommerce_active() && ( is_shop() || is_product_taxonomy() ) ) {
	$otb_title = woocommerce_page_title( false );
	if ( is_product_taxonomy() ) {
		$otb_description = term_description();
		$otb_eyebrow     = __( 'Shop', 'overtheball' );
		$otb_thumb       = absint( get_term_meta( get_queried_object_id(), 'thumbnail_id', true ) );
		if ( $otb_thumb ) {
			$otb_bg = $otb_thumb;
		}
	}
} elseif ( is_home() ) {
	$otb_blog  = (int) get_option( 'page_for_posts' );
	$otb_title = $otb_blog ? get_the_title( $otb_blog ) : __( 'Blog', 'overtheball' );
} elseif ( is_singular( 'post' ) ) {
	$otb_title = get_the_title();
} elseif ( is_singular() ) {
	$otb_title = get_the_title();
	if ( has_post_thumbnail() && 'image' === $otb_style ) {
		$otb_bg = get_post_thumbnail_id();
	}
} elseif ( is_search() ) {
	$otb_eyebrow = __( 'Search results', 'overtheball' );
	/* translators: %s: search query. */
	$otb_title = sprintf( __( '“%s”', 'overtheball' ), get_search_query() );
	global $wp_query;
	/* translators: %d: number of results. */
	$otb_description = sprintf( _n( '%d result found', '%d results found', (int) $wp_query->found_posts, 'overtheball' ), (int) $wp_query->found_posts );
} elseif ( is_404() ) {
	$otb_eyebrow = __( 'Oops — dropped a stitch', 'overtheball' );
	$otb_title   = __( 'Page not found', 'overtheball' );
} elseif ( is_archive() ) {
	$otb_title       = get_the_archive_title();
	$otb_description = get_the_archive_description();
}

if ( ! $otb_bg && 'image' === $otb_style ) {
	$otb_bg = absint( otb_option( 'page_header_image' ) );
}

$otb_classes = array(
	'page-header',
	'page-header--' . $otb_style,
	'page-header--' . otb_option( 'page_header_align' ),
);
if ( $otb_bg ) {
	$otb_classes[] = 'has-bg-image';
}
?>
<div class="<?php echo esc_attr( implode( ' ', $otb_classes ) ); ?>">
	<?php if ( $otb_bg ) : ?>
		<div class="page-header__bg" aria-hidden="true"><?php echo wp_get_attachment_image( $otb_bg, 'otb-hero', false, array( 'alt' => '' ) ); ?></div>
	<?php endif; ?>
	<div class="container page-header__inner">
		<?php otb_breadcrumbs(); ?>
		<?php
		if ( is_singular( 'post' ) ) {
			otb_entry_categories();
		} elseif ( $otb_eyebrow ) {
			echo '<p class="eyebrow">' . esc_html( $otb_eyebrow ) . '</p>';
		}
		?>
		<h1 class="page-title entry-title"><?php echo wp_kses_post( $otb_title ); ?></h1>
		<?php if ( is_singular( 'post' ) ) : ?>
			<?php otb_entry_meta(); ?>
		<?php elseif ( $otb_description ) : ?>
			<div class="page-header__description"><?php echo wp_kses_post( wpautop( $otb_description ) ); ?></div>
		<?php endif; ?>
	</div>
</div>
