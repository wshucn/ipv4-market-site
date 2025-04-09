<?php
    $post_container_class = [
        'uk-margin-medium-top',
        'uk-grid',
        'uk-grid-medium',
        'uk-height-match'
    ];
    if(array_key_exists('columns', $a)) $post_container_class[] = sprintf('uk-child-width-1-2@s uk-child-width-1-%s@l', $a['columns'], $a['columns']);

    $post_container_attributes = [
        'class'             => buildClass($post_container_class)
    ];

    $image = ( has_post_thumbnail(get_the_ID()) ? wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail' ) : '' );
?>
<div class="uk-position-relative sect-team">
    <?= buildAttributes($post_container_attributes, 'div') ?>
        {items}
            <div>
                <div class="uk-card uk-card-default uk-card-small uk-box-shadow-medium uk-padding-small uk-height-1-1 uk-border-radius">
                    <div>
                        <img class="uk-border-circle uk-width-2-3 uk-margin-auto uk-display-block uk-margin-small-bottom" data-src="<?php echo $image[0]; ?>" uk-img>
                        <div class="uk-text-center">
                            <h3 class="uk-margin-remove"><?php the_title(); ?></h3>

                            <?php if(get_field('title')) : ?>
                                <p class="uk-h3 uk-margin-small-bottom uk-margin-remove-top"><?php the_field('title'); ?></p>
                            <?php endif; ?>

                            <?php if(get_field('phone_number')) : ?>
                                <p class="uk-text-secondary uk-margin-remove"><?php the_field('phone_number'); ?></p>
                            <?php endif; ?>

                            <?php if(get_field('email')) : ?>
                                <p class="uk-margin-remove"><a href="mailto:<?php the_field('email'); ?>"><?php the_field('email'); ?></a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        {/items}
    </div>
</div>