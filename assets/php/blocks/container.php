<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make( __( 'UIkit Container' ) )
	->add_fields(
		array(
			Field::make( 'select', 'size', __( 'Size' ) )
				->set_width( 33 )
				->set_options(
					array(
						'small'  => __( 'Small' ),
						'large'  => __( 'Large' ),
						'normal' => __( 'Normal' ),
					)
				)
				->set_default_value( 'normal' ),
		)
	)
	->set_icon( 'layout' )
	->set_category( 'layout' )
	->set_keywords( array( __( 'block' ), __( 'container' ), __( 'content' ), __( 'group' ) ) )
	->set_inner_blocks( true )
	->set_render_callback(
		function ( $fields, $attributes, $inner_blocks ) {

			// Size.
			if ( 'normal' === $fields['size'] ) {
				$container_attrs['class'][] = 'uk-container';
			} else {
				$container_attrs['class'][] = sprintf( 'uk-container uk-container-%s', $fields['size'] );
			}
			?>

		<div <?php echo buildAttributes( $container_attrs ); ?>>

			<?php echo $inner_blocks; ?>

		</div><!-- /.uk-container -->

				<?php
		}
	);
