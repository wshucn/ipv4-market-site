<?php
/*
    Template Name: Blog Sidebar
*/

get_header();
?>
<!-- Content -->
<div class='content uk-container uk-container-large uk-margin-medium-top'>
	<div class="uk-padding uk-padding-remove-vertical" uk-grid>
		<div class="uk-width-2-3@m">
			<!-- Content -->
			<div>
				<?php
				if (have_posts()) :
					while (have_posts()) :
						the_post();
						// $content = apply_filters( 'the_content', get_the_content() );
						// echo $content;
						the_content();
					endwhile;
				endif;
				?>
			</div>
			<!-- END: Content -->
		</div>
		<div class="sidebar uk-width-1-3@m uk-flex-first@m ">

			<?php dynamic_sidebar('blog-sidebar'); ?>

		</div>
	</div>
</div>
<!-- END: Content -->
<?php
get_footer();
