<?php

/**
 * New Stats Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */
?>
<h1><?php echo the_field( 'symbol_before_number' ); ?>  </h1>
<p><?php echo the_field( 'number' ); ?>  </p>
