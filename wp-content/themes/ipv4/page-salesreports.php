<?php
/*
    Template Name: Reports Page
*/
get_header();
?>
<!-- Content -->
<div class="uk-background-muted uk-padding uk-padding-remove-horizontal">
    <div class='content uk-container uk-container-xlarge'>
        <div class="uk-container uk-container-large">
            <div class="uk-padding uk-padding-remove-vertical">
                <h1 class="uk-text-primary"><?php the_title(); ?></h1>
                <?php
                $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                $args = array(
                    'category__and' => '10',
                    'posts_per_page' =>  get_option('posts_per_page'),
                    'paged' => $paged
                );
                $my_posts = new WP_Query($args);
                if ($my_posts->have_posts()) : ?>
                    <div class="uk-child-width-1-2@m uk-grid-match" uk-grid>
                        <?php while($my_posts->have_posts()): $my_posts->the_post(); ?>
                            <div>
                                <div class="report uk-background-white uk-box-shadow-medium">
                                    <div class="uk-visible-toggle uk-overflow-hidden">
                                        <a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>" class="uk-position-relative uk-display-block">
                                            <?php
                                            $image = get_post_thumbnail_id(get_the_ID());
                                            if( get_post_thumbnail_id(get_the_ID()) ) {
                                                $image = ( has_post_thumbnail(get_the_ID()) ? wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'reports-archive' ) : '' );
                                                $image = $image[0];
                                            } else {
                                                $image = get_field('default_resource_thumbnail', 'options');
                                            } ?>

                                            <div class="img-wrap uk-position-relative">
                                                <img data-src="<?php echo $image; ?>" uk-img>
                                            </div>

                                            <div class="uk-overlay uk-overlay-primary uk-hidden-hover uk-position-cover uk-light">
                                                <span uk-icon="icon: arrow-right; ratio: 2" class="uk-position-absolute uk-position-center"></span>
                                            </div>
                                        </a>
                                    </div>

                                    <div class="content">
                                        <div class="uk-padding-small">
                                            <h2 class="uk-h3 uk-margin-remove"><a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>" class="uk-text-primary"><?php the_title(); ?></a></h2>

                                            <p><?php the_excerpt(); ?></p>

                                            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>"><strong>Read more</strong></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="sect-pagination uk-margin-large-top">
                        <?php mp_page_navi($my_posts); ?>
                    </div>
                <?php endif; wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
    <!-- END: Content -->
</div>

<?php
get_footer();
