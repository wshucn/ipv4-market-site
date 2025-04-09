<div uk-grid>
    <div class='uk-light uk-width-1 uk-width-auto@s uk-margin-small-left uk-margin-small-right'>
        <?php
            $logo_attrs = [
                'height' 	=> 57,
                'width' 	=> 278,
                'alt'		=> get_bloginfo( 'name' ),
                'uk-img'	=> 'data-src:/assets/images/logo.svg',
                'class'		=> 'uk-margin uk-width-1 uk-width-3-5@s',
                'role'		=> 'presentation',
                'uk-svg'	=> '',
            ];
        ?>
        <img <?= buildAttributes($logo_attrs) ?>>
        <?php get_template_part( 'partials/content', 'address' ); ?>
    </div>
    <div class='uk-width-1 uk-width-auto@s uk-margin-auto-left@s uk-margin-small-left uk-margin-small-right'>
        <div class='social'>
            <?php get_template_part( 'partials/content', 'social' ); ?>
        </div>
    </div>
</div>
