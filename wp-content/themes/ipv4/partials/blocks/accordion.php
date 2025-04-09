<?php
/**
 * Accordion Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */
?>

<?php $type = get_field('type_of_accordion'); ?>

<?php if(have_rows('accordion_elements')) : ?>
    <div class="<?php if($type == 'joined') : ?>uk-padding-small uk-background-white<?php endif; ?>">
        <ul class="<?php if($type == 'joined') : ?>joined-accordion<?php endif; ?>" uk-list uk-accordion>
            <?php while(have_rows('accordion_elements')) : the_row(); $title = get_sub_field('element_title'); $content = get_sub_field('element_content'); $tag = get_sub_field('element_title_tag'); ?>
                <li class="<?php if($type == 'separated') : ?>uk-background-white uk-display-block<?php endif; ?> uk-padding-small uk-margin-remove">
                    <a class="uk-accordion-title uk-flex uk-flex-middle uk-flex-between" href="#" <?php if($type == 'separated') : ?>style="padding-left:1em;"<?php endif; ?>><<?php echo $tag; ?> class="sans-serif uk-text-bold uk-h3 uk-margin-remove"><?php echo $title; ?></<?php echo $tag; ?>></a>
                    <div class="uk-accordion-content">
                        <?php echo $content; ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
<?php endif; ?>