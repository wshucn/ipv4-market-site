<?php
	$bottom_menu  = wp_nav_menu(
		array(
			'menu'           => __( 'Bottom Menu', 'wpbase' ),
			'theme_location' => 'bottom',   // must be registered with register_nav_menu()
			'items_wrap'     => '%3$s',
			'container'      => false,
			'echo'           => false,
		)
	);
	$privacy_link = ! empty( get_the_privacy_policy_link() ) ? list_items( array( get_the_privacy_policy_link() ), array( 'class' => 'menu-item' ) ) : '';
	?>
<div class='uk-width-2-3@s'>
	<?php if ( ! empty( $privacy_link ) || ! empty( $bottom_menu ) ) : ?>
		<ul class='bottom-menu uk-nav uk-flex uk-margin'>
		<?php
		if ( ! empty( $bottom_menu ) ) {
			echo $bottom_menu; }
		if ( ! empty( $privacy_link ) ) {
			echo $privacy_link[0]; }
		?>
		</ul>
	<?php endif; ?>
	<p class='uk-margin-remove uk-text-small'>
	<?php
	printf(
		'%s. &copy; %s %s. %s.',
		'Duane Morris LLP &amp; Affiliates',
		get_the_time( 'Y' ),
		'Duane Morris LLP',
		'Duane Morris is a registered service mark of Duane Morris LLP'
	);
	?>
	</p>
</div>
<div class='uk-width-auto@s uk-text-small'>
	<p>Powered by<br><img src='<?php echo get_asset_url( 'images/duane-morris.svg' ); ?>' alt='Duane Morris' width=178 height=28 uk-svg></p>
	<p class='uk-margin-remove'><a href='https://mediaproper.com/' target='_blank' rel='noopener'>Web Design</a> by Media Proper</p>
</div>
