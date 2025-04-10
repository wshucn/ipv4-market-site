<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
do_action( 'woocommerce_before_account_navigation' );
?>
<nav class='uk-padding-small uk-margin-medium-top'>
	<ul class='uk-nav'>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
<?php
$subnav_items_html = '';

$subnav_item_class = wc_get_account_menu_item_classes( $endpoint );
$item_url = esc_url( wc_get_account_endpoint_url( $endpoint ) );

$subnav_item_link_attrs = array(
	'href'  => $item_url,
);

if( isExternalURL($item_url)) {
	$subnav_item_link_attrs['target'] = '_blank';
}

$subnav_item_link = buildAttributes($subnav_item_link_attrs,'a', esc_html( $label ));

ob_start();
try {
	echo buildAttributes(
		[ 'class'   => $subnav_item_class ],
		'li', $subnav_item_link
	);
	$subnav_items_html .= ob_get_contents();
} finally {
	ob_end_clean();
}

// WooCommerce uses the 'is-active' class for the current menu item.
echo str_replace('is-active', 'uk-active', $subnav_items_html);

?>
		<?php endforeach; ?>
	</ul>
</nav>
<?php do_action( 'woocommerce_after_account_navigation' ); ?>
