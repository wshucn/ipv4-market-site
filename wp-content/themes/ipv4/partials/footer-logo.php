<?php
$logo_attrs = [
    'height'     => '50',
    'width'      => 'auto',
    'alt'        => get_bloginfo( 'name' ),
    'src'        => get_asset_url('images/logo.svg'),
    'class'      => buildClass($args),
    'uk-svg',
];
echo buildAttributes($logo_attrs, 'img');