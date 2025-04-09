<?php
$edit_post_link = get_edit_post_link();
if ($edit_post_link) : ?>
<div class='uk-position-bottom-right uk-position-small uk-position-fixed uk-position-z-index'>
    <div class='uk-inline'>
        <a class='uk-icon-button uk-button-danger' uk-icon='icon: menu' style='width: auto; height: auto; padding: 12px'
            title='<?php _e('Edit Menus'); ?>'
            href='<?php echo get_admin_url(null, 'nav-menus.php'); ?>'></a>
        <a class='uk-icon-button uk-button-danger' uk-icon='icon: cog' style='width: auto; height: auto; padding: 12px'
            title='<?php _e('Jump to Admin'); ?>'
            href='<?php echo get_admin_url(); ?>'></a>
        <a class='uk-icon-button uk-button-danger' uk-icon='icon: pencil; ratio: 1.5'
            title='<?php _e('Edit Page'); ?>'
            style='width: auto; height: auto; padding: 12px'
            href='<?php echo $edit_post_link; ?>'></a>
    </div>
</div>
<?php
endif;
