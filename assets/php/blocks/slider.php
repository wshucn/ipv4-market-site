<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make( __( 'UIkit Slider' ) )
	->add_fields(
		array(
			Field::make( 'media_gallery', 'slider_images', __( 'Slider' ) )
				->set_type( array( 'image' ) )
				->set_duplicates_allowed( false ),
			Field::make( 'number', 'columns', __( 'Columns' ) )
				->set_width( 33 )
				->set_min( 1 )
				->set_max( 6 )
				->set_default_value( 3 ),
			Field::make( 'select', 'align', __( 'Alignment' ) )
				->set_width( 33 )
				->set_options(
					array(
						'top'    => __( 'Top' ),
						'middle' => __( 'Middle' ),
						'bottom' => __( 'Bottom' ),
					)
				)
				->set_default_value( 'top' ),
			Field::make( 'select', 'gap', __( 'Items Gap' ) )
				->set_width( 33 )
				->set_options(
					array(
						'none'   => __( 'None' ),
						'small'  => __( 'Small' ),
						'medium' => __( 'Medium' ),
						'large'  => __( 'Large' ),
						'normal' => __( 'Normal' ),
					)
				)
				->set_default_value( 'none' ),
			Field::make( 'select', 'arrows_align', __( 'Arrows Alignment' ) )
				->set_width( 33 )
				->set_options(
					array(
						'top'    => __( 'Top' ),
						'center' => __( 'Middle' ),
						'bottom' => __( 'Bottom' ),
					)
				)
				->set_default_value( 'center' ),
			Field::make( 'select', 'arrows_offset', __( 'Arrows Offset' ) )
				->set_width( 33 )
				->set_options(
					array(
						'none'   => __( 'None' ),
						'small'  => __( 'Small' ),
						'medium' => __( 'Medium' ),
						'large'  => __( 'Large' ),
					)
				)
				->set_default_value( 'none' ),
			Field::make( 'select', 'arrows_position', __( 'Arrows Position' ) )
				->set_width( 33 )
				->set_options(
					array(
						'inside'  => __( 'Inside' ),
						'outside' => __( 'Outside' ),
						'overlay' => __( 'Overlay' ),
					)
				)
				->set_default_value( 'inside' ),
			Field::make( 'multiselect', 'slider_options', __( 'Slider Options' ) )
				->set_width( 100 )
				->add_options(
					array(
						'autoplay'       => 'Autoplay',
						'pause-on-hover' => 'Pause Autoplay on Hover',
						'finite'         => 'Finite Scrolling',
						'sets'           => 'Slide Sets',
						'center'         => 'Center Active',
						'draggable'      => 'Draggable',
					)
				)
				->set_default_value(
					array(
						'pause-on-hover',
						'draggable',
					)
				),
			Field::make( 'number', 'autoplay_interval', __( 'Autoplay Interval' ) )
				->set_width( 33 )
				->set_min( 0 )
				->set_max( 20000 )
				->set_step( 100 )
				->set_default_value( 7000 ),
			Field::make( 'number', 'velocity', __( 'Velocity' ) )
				->set_width( 33 )
				->set_min( 1 )
				->set_max( 100 )
				->set_default_value( 1 ),
		)
	)
	->set_icon( 'slides' )
	->set_category( 'media' )
	->set_keywords( array( __( 'block' ), __( 'image' ), __( 'content' ), __( 'slider' ) ) )
	->set_render_callback(
		function ( $fields, $attributes, $inner_blocks ) {

			$slider_attrs               = array(
				'uk-slider' => array(),
				'class'     => array( 'uk-margin-auto' ),
			);
			$container_attrs['class'][] = 'uk-slider-container';
			$list_attrs['class'][]      = 'uk-slider-items';

			// Slider Options.
			$slider_defaults = array(
				'autoplay'       => 'false',
				'pause-on-hover' => 'true',
				'finite'         => 'false',
				'sets'           => 'false',
				'center'         => 'false',
				'draggable'      => 'true',
			);
			// 1. Field will only contain selected ('true') options, so provide missing 'false' ones.
			// 2. Get slider options into associative array with 'true' values.
			// 3. Merge the provided ('true') and missing ('false') slider options.
			// 4. Filter out any values that are the same as defaults.
			// 5. Convert key => value to key: value for the attribute. (buildAttributes will do this, so comment out)
			$slider_options_false = array_fill_keys( array_keys( $slider_defaults ), 'false' );  // 1
			$slider_options       = array_fill_keys( $fields['slider_options'], 'true' );        // 2
			$slider_options       = array_merge( $slider_options_false, $slider_options );       // 3
			$slider_options       = array_diff_assoc( $slider_options, $slider_defaults );       // 4
			// $slider_attrs['uk-slider'][] = associative_to_attr( $slider_options );                      // 5
			$slider_attrs['uk-slider'] = $slider_options;

			// Autoplay Interval.
			if ( array_key_exists( 'autoplay_interval', $fields ) && 7000 !== $fields['autoplay_interval'] ) {
				$slider_attrs['uk-slider']['autoplay-interval'] = $fields['autoplay_interval'];
			}

			// Velocity.
			if ( array_key_exists( 'velocity', $fields ) && 1 !== $fields['velocity'] ) {
				  $slider_attrs['uk-slider']['velocity'] = $fields['velocity'];
			}

			// Columns.
			$list_attrs['class'][] = sprintf( 'uk-child-width-1-%s@m', $fields['columns'] );

			// Vertical Alignment.
			$list_attrs['class'][] = sprintf( 'uk-flex uk-flex-%s', $fields['align'] );

			// Items Gap.
			if ( 'none' !== $fields['gap'] ) {
				if ( 'normal' === $fields['gap'] ) {
					$list_attrs['class'][] = 'uk-grid';
				} else {
					$list_attrs['class'][] = sprintf( 'uk-grid uk-grid-%s', $fields['gap'] );
				}
			}

			// Arrows Inside/Outside.
			$arrows_align_pattern = 'uk-position-%s-%s';
			if ( 'outside' === $fields['arrows_position'] ) {
				$arrows_align_pattern .= '-out';
			} elseif ( 'inside' === $fields['arrows_position'] ) {
				$container_attrs['class'][] = 'uk-margin-xlarge-left uk-margin-xlarge-right';
			}
			?>

		<div <?php echo buildAttributes( $slider_attrs ); ?>>

			<div class='uk-position-relative'>

				<div <?php echo buildAttributes( $container_attrs ); ?>>

					<ul <?php echo buildAttributes( $list_attrs ); ?>>

						<?php foreach ( $fields['slider_images'] as $id ) : ?>

							<?php
							$image = wp_get_attachment_image( $id, 'full' );
							if ( $image ) :
								?>

						<li>
								<?php

								// Does the Media Library image have a URL custom field?
								if ( get_field( 'attachment_url_url', $id ) ) {
									$url        = get_field( 'attachment_url_url', $id );
									$target     = get_field( 'attachment_url_target', $id );
									$nofollow   = get_field( 'attachment_url_nofollow', $id );
									$link_attrs = array(
										'href' => esc_url( $url ),
									);
									if ( $target ) {
										$link_attrs['target'] = '_blank'; }
									if ( $nofollow ) {
										$link_attrs['rel'] = 'nofollow'; }
									$image = buildAttributes( $link_attrs, 'a', $image );
								}

								echo wp_kses_post( $image );

								?>
						</li>

							<?php endif; ?>
						<?php endforeach; ?>

					</ul><!-- /.uk-slider-items -->

				</div><!-- /.uk-slider-container -->

				<?php
				foreach ( array( 'previous', 'next' ) as $direction ) :
					$slidenav_arrow_attrs = array(
						'href'           => '#',
						'uk-slider-item' => $direction,
						"uk-slidenav-{$direction}",
					);

					// Vertical Position & Outside/Inside.
					$slidenav_arrow_attrs['class'][] = sprintf( $arrows_align_pattern, $fields['arrows_align'], $direction === 'next' ? 'right' : 'left' );

					// Offset.
					if ( 'none' !== $fields['arrows_offset'] ) {
						$slidenav_arrow_attrs['class'][] = sprintf( 'uk-position-%s', $fields['arrows_offset'] );
					}
					?>
				<a <?php echo buildAttributes( $slidenav_arrow_attrs ); ?>></a>
				<?php endforeach; ?>

			</div>

		</div><!-- /uk-slider -->

				<?php
		}
	);
