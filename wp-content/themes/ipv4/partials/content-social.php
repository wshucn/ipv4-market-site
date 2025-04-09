<!-- social links block -->
<div class='social hide-link-text uk-margin-top uk-margin-bottom uk-flex uk-flex-wrap uk-flex-gap-small uk-light'>
	<?php if ( have_rows( 'social_media_links', 'options' ) ) :
		while ( have_rows( 'social_media_links', 'options' ) ) :
			the_row();
			$link_type = get_sub_field( 'link_type' );
			if ( get_sub_field( 'link_icon' ) ) {
				$icon_text = get_sub_field( 'link_icon' );
			} else {
				$icon_text = 'icon: ' . $link_type;
			}

			the_contact_social( $icon_text, $link_type, array() );
	endwhile;
endif; ?>
</div>
