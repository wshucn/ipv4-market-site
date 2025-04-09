<?php
/**
 * Description tab
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

global $post;

// $heading = apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'woocommerce' ) );

?>

<?php if ( !empty($heading) ) : ?>
	<h2><?php echo esc_html( $heading ); ?></h2>
<?php endif; ?>

<?php the_content(); ?>

<?php
// Output links for downloads attached to this product
$downloads = get_field('download');
if(!empty($downloads)) { ?>
	<h4><?php _e('Downloads', 'text_domain'); ?></h4>
	<?php
	ob_start();
	foreach($downloads as $download): ?>
	<li>
		<a href='<?php echo esc_url($download['file']) ?>' class='uk-button uk-button-link' aria-label='<?php _e('Download' . sanitize_text_field($download['label']), 'text_domain') ?>'>
		<span class='uk-margin-small-right' uk-icon='cloud-download'></span>
		<?php _e(sanitize_text_field($download['label']), 'text_domain') ?>
		</a>
	</li>
	<?php
	endforeach;
	// Wrap the links in a grid.
	echo buildAttributes([ 'class' => 'uk-list' ], 'ul', ob_get_clean() );
}

?>