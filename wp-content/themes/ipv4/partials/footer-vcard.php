<div class='uk-text-center uk-flex uk-flex-center uk-flex-middle uk-padding-large uk-light'>
    <div class='vcard'>
        <?php
            $logo_attrs = [
                'height' 	=> 190,
                'width' 	=> 612,
                'alt'		=> get_bloginfo( 'name' ),
                'uk-img'	=> 'data-src:/assets/images/logo.svg',
                'class'		=> 'logo-inverse uk-margin',
                'role'		=> 'presentation',
                'uk-svg'	=> '',
            ];
        ?>
        <img <?= buildAttributes($logo_attrs) ?>>
        <?php get_template_part( 'partials/content', 'address' ); ?>
        <div class='social uk-margin'>
            <?php get_template_part( 'partials/content', 'social' ); ?>
        </div>
    </div>
</div>
