<?php
/**
 * Checkout shop policies menu area.
 */

defined( 'ABSPATH' ) || exit;

$policy_page_ids = array(
	get_option( 'wp_page_for_privacy_policy' ),
	get_option( 'woocommerce_shipping_returns_page_id' ),
	wc_terms_and_conditions_page_id(),
);
$policy_page_ids = array_filter( $policy_page_ids );

$subnav_items = array();
foreach ( $policy_page_ids as $policy_page_id ) {
	$policy_page = get_post( $policy_page_id );
	if ( $policy_page->post_content ) : // check for policy page content
		$item_title = get_the_title( $policy_page_id );
		$modal_id   = sprintf( 'modal-%s', sanitize_title_with_dashes( $item_title ) );
		?>
	<div id='<?php echo esc_attr( $modal_id ); ?>' uk-modal>
		<div class='uk-modal-dialog uk-width-auto uk-padding uk-border-rounded'>
			<button class="uk-modal-close-default" type="button" uk-close></button>
			<div class="uk-modal-header">
				<h2 class="alt uk-modal-title"><?php echo $item_title; ?></h2>
			</div>
			<hr>
			<div class='uk-modal-body' uk-overflow-auto>
			<?php
			echo apply_filters( 'the_content', $policy_page->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			</div>
		</div>
	</div>
		<?php
		$subnav_items[] = buildAttributes(
			array(
				'href' => "#{$modal_id}",
				'uk-toggle',
			),
			'a',
			$item_title
		);
	endif; // check for policy page content
}

if ( ! empty( $subnav_items ) ) :
	?>
<div class='policy-pages-menu uk-text-meta'>
	<hr>
	<ul class='uk-subnav uk-margin-remove' uk-margin>
		<?php foreach ( $subnav_items as $subnav_item ) : ?>
		<li><?php echo wp_kses_post( $subnav_item ); ?></li>
		<?php endforeach; ?>
	</ul>
</div>
	<?php
endif;
