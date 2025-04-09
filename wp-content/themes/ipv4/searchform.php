<form role="search" aria-label="" method="get" class="search-form uk-search uk-search-default uk-width-1-1" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<!-- <span hidden class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ); ?></span> -->
		<span uk-search-icon></span>
		<input type="search" data-swpparentel=".navbar-search-results" class="search-field uk-input uk-search-input" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	</label>
	<!-- <input type="submit" class="search-submit uk-button" value="<?php echo esc_attr_x( 'Search', 'submit button' ); ?>" /> -->
</form>
