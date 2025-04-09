<?php
/**
 * WooCommerce Product Reviews
 *
 * @package woocommerce
 */

// Change Gravatar class for reviews.
function mp_woocommerce_review_display_gravatar( $comment ) {
	echo get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '64' ), '', '', array( 'class' => 'uk-comment-avatar' ) );
}
