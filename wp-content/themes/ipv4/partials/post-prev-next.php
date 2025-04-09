<div class="next-prev">
	<?php
    /**
    * Infinite next and previous post looping in WordPress
    */
    if( get_adjacent_post(false, '', true) ) {
    ?><div class="nav-previous"><?php
    previous_post_link('%link', '&larr; Previous Post');
    ?></span></div><?php 
    } else {
    $first = new WP_Query('posts_per_page=1&order=DESC&post_type=post&category_name=blog'); $first->the_post();
    echo '<div class="nav-previous"><a href="' . get_permalink() . '" class="alpha">&larr; Previous Post</a></span></div>';
    wp_reset_query();
    }
    if( get_adjacent_post(false, '', false) ) {
    ?><div class="nav-next"><?php 
    next_post_link('%link', 'Next Post &rarr;');
    ?></span></div><?php
    } else {
    $last = new WP_Query('posts_per_page=1&order=ASC&post_type=post&category_name=blog'); $last->the_post();
    echo '<div class="nav-next"><a href="' . get_permalink() . '" class="alpha">Next Post &rarr;</a></span></div>';
    wp_reset_query();
    } 
	?>
</div>