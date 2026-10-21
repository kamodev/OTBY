<?php
/**
 * Template tags used by the theme's templates.
 *
 * @package OverTheBall
 */

defined( 'ABSPATH' ) || exit;

/**
 * Logo <img> markup for a variant.
 *
 * @param string $variant default|light.
 * @return string
 */
function otb_get_logo_image( $variant = 'default' ) {
	$name  = get_bloginfo( 'name', 'display' );
	$class = 'otb-logo otb-logo--' . $variant;

	$id = 'light' === $variant ? absint( otb_option( 'logo_light' ) ) : absint( get_theme_mod( 'custom_logo' ) );

	if ( $id && wp_attachment_is_image( $id ) ) {
		return wp_get_attachment_image(
			$id,
			'full',
			false,
			array(
				'class'         => $class,
				'alt'           => $name,
				'loading'       => false,
				'fetchpriority' => 'default' === $variant ? 'high' : 'auto',
				'decoding'      => 'async',
			)
		);
	}

	$file = 'light' === $variant ? 'logo-light.png' : 'logo.png';

	return sprintf(
		'<img class="%1$s" src="%2$s" width="806" height="380" alt="%3$s" decoding="async"%4$s>',
		esc_attr( $class ),
		esc_url( OTB_URI . 'assets/images/' . $file ),
		esc_attr( $name ),
		'default' === $variant ? ' fetchpriority="high"' : ''
	);
}

/**
 * Site branding: logo (plus a light variant for transparent/dark headers).
 *
 * @param bool $with_light Also print the light logo variant.
 * @param bool $with_title Print the (visually hidden) site title.
 */
function otb_site_branding( $with_light = false, $with_title = true ) {
	$tag = is_front_page() && ! otb_home_sections_active() ? 'h1' : 'p';
	?>
	<div class="site-branding">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php
			echo otb_get_logo_image( 'default' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in otb_get_logo_image().
			if ( $with_light ) {
				echo otb_get_logo_image( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in otb_get_logo_image().
			}
			?>
		</a>
		<?php if ( $with_title ) : ?>
			<<?php echo esc_attr( $tag ); ?> class="site-title screen-reader-text"><?php bloginfo( 'name' ); ?></<?php echo esc_attr( $tag ); ?>>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Small eyebrow (script accent) + heading + optional intro for sections.
 *
 * @param array $args eyebrow, title, text, align, link_text, link_url, tag, id.
 */
function otb_section_heading( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow'   => '',
			'title'     => '',
			'text'      => '',
			'align'     => 'center',
			'link_text' => '',
			'link_url'  => '',
			'tag'       => 'h2',
			'id'        => '',
		)
	);

	if ( ! $args['title'] && ! $args['eyebrow'] ) {
		return;
	}

	$tag = in_array( $args['tag'], array( 'h1', 'h2', 'h3' ), true ) ? $args['tag'] : 'h2';
	?>
	<header class="section-heading section-heading--<?php echo esc_attr( $args['align'] ); ?>">
		<?php if ( $args['eyebrow'] ) : ?>
			<p class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
		<?php endif; ?>
		<?php if ( $args['title'] ) : ?>
			<<?php echo esc_attr( $tag ); ?> class="section-title"<?php echo $args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : ''; ?>><?php echo esc_html( $args['title'] ); ?></<?php echo esc_attr( $tag ); ?>>
		<?php endif; ?>
		<?php if ( $args['text'] ) : ?>
			<p class="section-intro"><?php echo esc_html( $args['text'] ); ?></p>
		<?php endif; ?>
		<?php if ( $args['link_text'] && $args['link_url'] ) : ?>
			<a class="section-link" href="<?php echo esc_url( $args['link_url'] ); ?>"><?php echo esc_html( $args['link_text'] ); ?> <?php otb_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Print a button link.
 *
 * @param string $text  Label.
 * @param string $url   URL.
 * @param string $style primary|secondary|outline|light|ghost.
 */
function otb_button( $text, $url, $style = 'primary' ) {
	if ( ! $text || ! $url ) {
		return;
	}
	printf(
		'<a class="btn btn--%1$s" href="%2$s">%3$s</a>',
		esc_attr( $style ),
		esc_url( $url ),
		esc_html( $text )
	);
}

/**
 * Post date.
 */
function otb_posted_on() {
	$time = sprintf(
		'<time class="entry-date published" datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);
	printf( '<span class="posted-on">%s</span>', $time ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
}

/**
 * Post author.
 */
function otb_posted_by() {
	// Read the author from the post so this also works in the title banner, before the loop.
	$author_id = (int) get_post_field( 'post_author', get_the_ID() );
	if ( ! $author_id ) {
		return;
	}
	printf(
		'<span class="byline">%1$s <a class="url fn n" href="%2$s">%3$s</a></span>',
		esc_html_x( 'by', 'post author', 'overtheball' ),
		esc_url( get_author_posts_url( $author_id ) ),
		esc_html( get_the_author_meta( 'display_name', $author_id ) )
	);
}

/**
 * Entry meta line honoring the Customizer toggles.
 */
function otb_entry_meta() {
	if ( 'post' !== get_post_type() ) {
		return;
	}
	echo '<div class="entry-meta">';
	if ( otb_option( 'meta_date' ) ) {
		otb_posted_on();
	}
	if ( otb_option( 'meta_author' ) ) {
		otb_posted_by();
	}
	if ( otb_option( 'meta_comments' ) && comments_open() && get_comments_number() ) {
		echo '<span class="comments-link">';
		comments_popup_link();
		echo '</span>';
	}
	echo '</div>';
}

/**
 * Category pills above a post title.
 */
function otb_entry_categories() {
	if ( 'post' !== get_post_type() || ! otb_option( 'meta_categories' ) ) {
		return;
	}
	$categories = get_the_category_list( ' ' );
	if ( $categories ) {
		echo '<div class="entry-categories">' . $categories . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped list.
	}
}

/**
 * Tags and edit link below single posts.
 */
function otb_entry_footer() {
	if ( 'post' === get_post_type() && otb_option( 'single_tags' ) ) {
		$tags = get_the_tag_list( '', ' ' );
		if ( $tags ) {
			echo '<div class="entry-tags"><span class="screen-reader-text">' . esc_html__( 'Tags:', 'overtheball' ) . '</span>' . $tags . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped list.
		}
	}
	edit_post_link(
		sprintf(
			/* translators: %s: post title. */
			esc_html__( 'Edit %s', 'overtheball' ),
			'<span class="screen-reader-text">' . get_the_title() . '</span>'
		),
		'<span class="edit-link">',
		'</span>'
	);
}

/**
 * Numbered pagination for archives.
 */
function otb_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => otb_get_icon( 'chevron-left', array( 'size' => 18 ) ) . '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'overtheball' ) . '</span>',
			'next_text'          => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'overtheball' ) . '</span>' . otb_get_icon( 'chevron-right', array( 'size' => 18 ) ),
			'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'overtheball' ) . ' </span>',
		)
	);
}

/**
 * Breadcrumb trail. Uses Yoast / Rank Math / WooCommerce when available.
 */
function otb_breadcrumbs() {
	if ( ! otb_option( 'breadcrumbs' ) || is_front_page() || '1' === otb_page_meta( 'hide_breadcrumbs' ) ) {
		return;
	}

	if ( function_exists( 'yoast_breadcrumb' ) && current_theme_supports( 'yoast-seo-breadcrumbs' ) ) {
		yoast_breadcrumb( '<nav class="otb-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'overtheball' ) . '">', '</nav>' );
		return;
	}

	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		echo '<nav class="otb-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'overtheball' ) . '">';
		rank_math_the_breadcrumbs();
		echo '</nav>';
		return;
	}

	if ( otb_is_woocommerce_page() && function_exists( 'woocommerce_breadcrumb' ) ) {
		woocommerce_breadcrumb(
			array(
				'wrap_before' => '<nav class="otb-breadcrumbs woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'overtheball' ) . '"><ol>',
				'wrap_after'  => '</ol></nav>',
				'before'      => '<li>',
				'after'       => '</li>',
				'delimiter'   => '',
			)
		);
		return;
	}

	$trail = otb_breadcrumb_trail();
	if ( count( $trail ) < 2 ) {
		return;
	}

	echo '<nav class="otb-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'overtheball' ) . '"><ol>';
	$last = count( $trail ) - 1;
	foreach ( $trail as $i => $crumb ) {
		if ( $i === $last || empty( $crumb['url'] ) ) {
			printf( '<li><span%s>%s</span></li>', $i === $last ? ' aria-current="page"' : '', esc_html( $crumb['label'] ) );
		} else {
			printf( '<li><a href="%s">%s</a></li>', esc_url( $crumb['url'] ), esc_html( $crumb['label'] ) );
		}
	}
	echo '</ol></nav>';
}

/**
 * Build the breadcrumb trail for non-WooCommerce views.
 *
 * @return array[] Each with label and url.
 */
function otb_breadcrumb_trail() {
	$trail     = array(
		array(
			'label' => __( 'Home', 'overtheball' ),
			'url'   => home_url( '/' ),
		),
	);
	$blog_page = (int) get_option( 'page_for_posts' );

	$add_term_ancestors = static function ( $term ) use ( &$trail ) {
		foreach ( array_reverse( get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' ) ) as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, $term->taxonomy );
			if ( $ancestor && ! is_wp_error( $ancestor ) ) {
				$trail[] = array(
					'label' => $ancestor->name,
					'url'   => get_term_link( $ancestor ),
				);
			}
		}
	};

	if ( is_home() ) {
		$trail[] = array(
			'label' => $blog_page ? get_the_title( $blog_page ) : __( 'Blog', 'overtheball' ),
			'url'   => '',
		);
	} elseif ( is_singular( 'post' ) ) {
		if ( $blog_page ) {
			$trail[] = array(
				'label' => get_the_title( $blog_page ),
				'url'   => get_permalink( $blog_page ),
			);
		}
		$categories = get_the_category();
		if ( $categories ) {
			$add_term_ancestors( $categories[0] );
			$trail[] = array(
				'label' => $categories[0]->name,
				'url'   => get_category_link( $categories[0] ),
			);
		}
		$trail[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
			$trail[] = array(
				'label' => get_the_title( $ancestor ),
				'url'   => get_permalink( $ancestor ),
			);
		}
		$trail[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_singular() ) {
		$type = get_post_type_object( get_post_type() );
		if ( $type && $type->has_archive ) {
			$trail[] = array(
				'label' => $type->labels->name,
				'url'   => get_post_type_archive_link( $type->name ),
			);
		}
		$trail[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $blog_page && ( is_category() || is_tag() ) ) {
			$trail[] = array(
				'label' => get_the_title( $blog_page ),
				'url'   => get_permalink( $blog_page ),
			);
		}
		$add_term_ancestors( $term );
		$trail[] = array(
			'label' => $term->name,
			'url'   => '',
		);
	} elseif ( is_search() ) {
		$trail[] = array(
			/* translators: %s: search query. */
			'label' => sprintf( __( 'Search: %s', 'overtheball' ), get_search_query() ),
			'url'   => '',
		);
	} elseif ( is_404() ) {
		$trail[] = array(
			'label' => __( 'Page not found', 'overtheball' ),
			'url'   => '',
		);
	} elseif ( is_author() ) {
		$trail[] = array(
			'label' => get_the_author_meta( 'display_name', (int) get_query_var( 'author' ) ),
			'url'   => '',
		);
	} elseif ( is_post_type_archive() ) {
		$trail[] = array(
			'label' => post_type_archive_title( '', false ),
			'url'   => '',
		);
	} elseif ( is_archive() ) {
		$trail[] = array(
			'label' => wp_strip_all_tags( get_the_archive_title() ),
			'url'   => '',
		);
	}

	return $trail;
}

/**
 * Social icon links.
 *
 * @param string $extra_class Extra class.
 */
function otb_social_links( $extra_class = '' ) {
	$links = otb_active_social_links();
	if ( ! $links ) {
		return;
	}
	$networks = otb_social_networks();
	$target   = otb_option( 'social_new_tab' ) ? ' target="_blank" rel="noopener me"' : ' rel="me"';

	echo '<ul class="otb-social ' . esc_attr( $extra_class ) . '">';
	foreach ( $links as $network => $url ) {
		printf(
			'<li><a href="%1$s"%2$s aria-label="%3$s">%4$s</a></li>',
			esc_url( $url ),
			$target, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static string.
			esc_attr( $networks[ $network ] ),
			otb_get_icon( $network, array( 'size' => 18 ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		);
	}
	echo '</ul>';
}

/**
 * Contact details list (phone, email, address, hours).
 *
 * @param array $args 'class', 'fields'.
 */
function otb_contact_list( $args = array() ) {
	$args  = wp_parse_args(
		$args,
		array(
			'class'  => '',
			'fields' => array( 'phone', 'email', 'address', 'hours' ),
		)
	);
	$items = '';

	foreach ( $args['fields'] as $field ) {
		$value = trim( (string) otb_option( 'contact_' . $field ) );
		if ( '' === $value ) {
			continue;
		}
		switch ( $field ) {
			case 'phone':
				$inner = sprintf( '<a href="tel:%s">%s</a>', esc_attr( preg_replace( '/[^0-9+]/', '', $value ) ), esc_html( $value ) );
				$icon  = 'phone';
				break;
			case 'email':
				$inner = sprintf( '<a href="mailto:%1$s">%1$s</a>', esc_html( antispambot( $value ) ) );
				$icon  = 'mail';
				break;
			case 'address':
				$inner = nl2br( esc_html( $value ) );
				$icon  = 'map-pin';
				break;
			default:
				$inner = nl2br( esc_html( $value ) );
				$icon  = 'clock';
		}
		$items .= '<li class="otb-contact__' . esc_attr( $field ) . '">' . otb_get_icon( $icon, array( 'size' => 18 ) ) . '<span>' . $inner . '</span></li>';
	}

	if ( $items ) {
		echo '<ul class="otb-contact ' . esc_attr( $args['class'] ) . '">' . $items . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
}

/**
 * Newsletter form: custom shortcode, MailPoet form, or an admin hint.
 *
 * @return bool Whether a form was printed.
 */
function otb_newsletter_form() {
	$shortcode = trim( (string) otb_option( 'newsletter_shortcode' ) );
	if ( $shortcode ) {
		echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode output.
		return true;
	}

	if ( function_exists( 'otb_mailpoet_form_id' ) && shortcode_exists( 'mailpoet_form' ) ) {
		$form_id = otb_mailpoet_form_id();
		if ( $form_id ) {
			echo do_shortcode( '[mailpoet_form id="' . absint( $form_id ) . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode output.
			return true;
		}
	}

	if ( current_user_can( 'edit_theme_options' ) ) {
		printf(
			'<p class="otb-admin-hint">%s <a href="%s">%s</a></p>',
			esc_html__( 'Install MailPoet and create a form, or add any newsletter shortcode, to show a signup form here.', 'overtheball' ),
			esc_url( admin_url( 'customize.php?autofocus[section]=otb_contact_forms' ) ),
			esc_html__( 'Choose a form', 'overtheball' )
		);
	}
	return false;
}

/**
 * Accepted payment methods (image or labelled chips).
 */
function otb_payment_icons() {
	if ( ! otb_option( 'payment_icons' ) ) {
		return;
	}
	$image = absint( otb_option( 'payment_image' ) );
	if ( $image ) {
		echo '<div class="otb-payments">' . wp_get_attachment_image( $image, 'medium', false, array( 'alt' => esc_attr__( 'Accepted payment methods', 'overtheball' ) ) ) . '</div>';
		return;
	}
	$methods = array_filter( array_map( 'trim', explode( ',', (string) otb_option( 'payment_methods' ) ) ) );
	if ( ! $methods ) {
		return;
	}
	echo '<ul class="otb-payments" aria-label="' . esc_attr__( 'Accepted payment methods', 'overtheball' ) . '">';
	foreach ( $methods as $method ) {
		echo '<li>' . esc_html( $method ) . '</li>';
	}
	echo '</ul>';
}

/**
 * Share links (no third-party scripts).
 */
function otb_share_buttons() {
	if ( ! otb_option( 'share_buttons' ) ) {
		return;
	}
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( wp_strip_all_tags( get_the_title() ) );
	$image = rawurlencode( (string) get_the_post_thumbnail_url( null, 'large' ) );

	$links = array(
		'facebook'  => array( __( 'Share on Facebook', 'overtheball' ), 'https://www.facebook.com/sharer/sharer.php?u=' . $url ),
		'pinterest' => array( __( 'Pin it', 'overtheball' ), 'https://pinterest.com/pin/create/button/?url=' . $url . '&media=' . $image . '&description=' . $title ),
		'x'         => array( __( 'Share on X', 'overtheball' ), 'https://x.com/intent/post?url=' . $url . '&text=' . $title ),
		'mail'      => array( __( 'Share by email', 'overtheball' ), 'mailto:?subject=' . $title . '&body=' . $url ),
	);

	echo '<div class="otb-share"><span class="otb-share__label">' . esc_html__( 'Share', 'overtheball' ) . '</span><ul>';
	foreach ( $links as $icon => $link ) {
		printf(
			'<li><a href="%1$s" target="_blank" rel="noopener nofollow" aria-label="%2$s">%3$s</a></li>',
			esc_url( $link[1] ),
			esc_attr( $link[0] ),
			otb_get_icon( $icon, array( 'size' => 18 ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		);
	}
	echo '</ul></div>';
}

/**
 * Author bio box below single posts.
 */
function otb_author_box() {
	if ( ! otb_option( 'single_author_box' ) || ! get_the_author_meta( 'description' ) ) {
		return;
	}
	?>
	<aside class="otb-author-box">
		<?php echo get_avatar( get_the_author_meta( 'ID' ), 96 ); ?>
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Written by', 'overtheball' ); ?></p>
			<h2 class="otb-author-box__name"><?php the_author(); ?></h2>
			<p><?php echo wp_kses_post( get_the_author_meta( 'description' ) ); ?></p>
			<a class="section-link" href="<?php echo esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ); ?>"><?php esc_html_e( 'More posts', 'overtheball' ); ?> <?php otb_icon( 'arrow-right', array( 'size' => 16 ) ); ?></a>
		</div>
	</aside>
	<?php
}

/**
 * Related posts (same category) below single posts.
 */
function otb_related_posts() {
	if ( ! otb_option( 'single_related' ) ) {
		return;
	}
	$categories = wp_get_post_categories( get_the_ID() );
	if ( ! $categories ) {
		return;
	}
	$related = new WP_Query(
		array(
			'category__in'        => $categories,
			'post__not_in'        => array( get_the_ID() ),
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( ! $related->have_posts() ) {
		return;
	}
	?>
	<section class="otb-related" aria-labelledby="otb-related-title">
		<?php
		otb_section_heading(
			array(
				'eyebrow' => __( 'Keep reading', 'overtheball' ),
				'title'   => __( 'Related Posts', 'overtheball' ),
				'id'      => 'otb-related-title',
			)
		);
		?>
		<div class="post-grid post-grid--3">
			<?php
			while ( $related->have_posts() ) {
				$related->the_post();
				get_template_part( 'template-parts/content/card' );
			}
			wp_reset_postdata();
			?>
		</div>
	</section>
	<?php
}

/**
 * Bundled decorative art used when an image slot is empty.
 *
 * @param string $extra_class Extra class.
 */
function otb_placeholder_art( $extra_class = '' ) {
	printf(
		'<div class="otb-placeholder %1$s" aria-hidden="true"><img src="%2$s" alt="" width="512" height="512" loading="lazy" decoding="async"></div>',
		esc_attr( $extra_class ),
		esc_url( OTB_URI . 'assets/images/site-icon.png' )
	);
}
