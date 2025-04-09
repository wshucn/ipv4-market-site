<!-- vcard -->
<div class='vcard' style='display: none;'>
    <div class='fn'><?= get_bloginfo( 'name' ); ?></div>
    <div class='org'><?= get_bloginfo( 'name' ); ?></div>
    <div class='tel'><span class='type'>Work</span> <?= wp_cache_get( 'contact_phone' ); ?></div>
    <div>Email:<span class='email'><?= wp_cache_get( 'contact_email' ); ?></span></div>
</div>
