<div class='<?= buildClass('copyright', $args) ?>'>
    <?php
    // Use Organization Name set in Yoast, or otherwise the site name.
    if(class_exists('WPSEO_Options')) $yoast_seo_company_name = WPSEO_Options::get( 'company_name', '' );
    $organization_name = empty($yoast_seo_company_name) ? get_bloginfo( 'name' ) : $yoast_seo_company_name;

    printf(__('Copyright', 'text_domain') . ' &copy; %s %s. ' . __('All rights reserved', 'text_domain') . '.',
        date('Y'),
        rtrim($organization_name, '.')
    );

    ?>
</div>
