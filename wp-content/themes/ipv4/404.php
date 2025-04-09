<?php get_header(); ?>

<!-- Content -->
<div class='content uk-width-expand'>
	<div class='uk-section uk-text-center'>
		<div class='uk-container uk-container-expand'>
			<div class='uk-container uk-container-small'>
				<h1 class='uk-margin-remove alt uk-text-secondary'><?php _e('THIS PAGE DOESN\'T SEEM TO EXIST.', 'text_domain'); ?>
				</h1>
				<p class='uk-text-large uk-text-bold'><?php _e('It looks like the link pointing here was faulty. Maybe try searching?', 'text_domain'); ?>
				</p>
                <?php get_search_form(); ?>
			</div>
		</div>
	</div>
</div>
<!-- END: Content -->

<?php
get_footer();
