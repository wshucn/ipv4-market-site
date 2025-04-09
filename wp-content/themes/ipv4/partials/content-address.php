<!-- address links block -->
<address class='<?= buildClass('uk-margin', $args) ?>'>
    <?php the_contact_address('',0); ?>
        <div class='single-line'>
            <span><?php the_contact_phone('',0); ?></span>
            <span><?php the_contact_email('',0); ?></span>
        </div>
</address>
