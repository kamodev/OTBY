<?php
/**
 * Title: Welcome banner
 * Slug: overtheball/intro-banner
 * Categories: overtheball, banner
 * Description: Lavender banner with a script greeting, heading, text and buttons.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","backgroundColor":"lavender","style":{"spacing":{"padding":{"top":"88px","bottom":"88px","left":"24px","right":"24px"}}},"layout":{"type":"constrained","contentSize":"720px"}} -->
<div class="wp-block-group alignfull has-lavender-background-color has-background" style="padding-top:88px;padding-right:24px;padding-bottom:88px;padding-left:24px"><!-- wp:paragraph {"align":"center","textColor":"primary","fontFamily":"script","fontSize":"xx-large"} -->
<p class="has-text-align-center has-primary-color has-text-color has-script-font-family has-xx-large-font-size"><?php esc_html_e( 'Hello, maker', 'overtheball' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'Yarn and fiber for your next favorite project', 'overtheball' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php esc_html_e( 'From cozy worsted to whisper-light lace, find the right yarn, fiber and notions for whatever is next on your needles or hook.', 'overtheball' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( otb_shop_url() ); ?>"><?php esc_html_e( 'Shop yarn', 'overtheball' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Ask a question', 'overtheball' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
