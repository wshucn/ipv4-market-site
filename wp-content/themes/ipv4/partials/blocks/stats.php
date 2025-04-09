<?php

/**
 * Stats Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */
?>

<?php wp_enqueue_script( 'counter' ); ?>

<div class="numbers numAnim">
	<div class="number uk-flex uk-flex-middle uk-flex-center">
		<p class="head uk-margin-remove uk-text-xlarge uk-text-primary uk-text-bold">
		<?php
		if ( get_field( 'symbol_before_number' ) ) :
			echo get_field( 'symbol_before_number' );
endif;
		?>
<?php
if ( ! get_field( 'animate' ) ) :
	?>
	<span><?php echo get_field( 'number' ); ?></span>
	<?php
elseif ( get_field( 'animate' ) ) :
	?>
	<span class="num-anim" data-num="<?php echo get_field( 'number' ); ?>">0</span>
	<?php
endif;
if ( get_field( 'symbol_after_number' ) ) :
	echo get_field( 'symbol_after_number' );
endif;
?>
</p>
		<p class="sub uk-margin-remove uk-text-xlarge uk-text-primary uk-text-bold" style="padding-left:.35em;"><?php echo get_field( 'number_text' ); ?></p>
	</div>
</div>

<script>
	// This code block ensures that the number animations trigger when the user scrolls.
	// It waits until the document is fully loaded before attaching the scroll event handler.
	jQuery(document).ready(function() {
		// Attach a scroll event handler to the window.
		jQuery(window).scroll(function() {
			// Check if the scroll position is greater than the position of .numAnim minus 800 pixels.
			if (jQuery(window).scrollTop() > (jQuery(".numAnim").offset().top - 800)) {
				// For each element with the class .num-anim, perform the following actions.
				jQuery(".num-anim").each(function() {
				var newNumber = jQuery(this).data('num')
				console.log(newNumber)

					// Retrieve the number from the data attribute 'num' and parse it as an integer.
					// var num = parseInt(jQuery(this).data("num"), 10);
					if(newNumber === 1.4) {
						jQuery(this).removeClass()
					}
					jQuery(this).animateNumbers(newNumber, true, 1250, "linear");

					// jQuery(this).animateNumbers(newNumber, true, 1250, "linear");
					// Animate the number from 0 to the retrieved number over 1250 milliseconds using the 'swing' easing function.
				});
			}
		});
	});
</script>
