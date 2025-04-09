        </div><!-- END: Grid -->
    </div><!-- END: Container -->
</main>

<?php if ( is_active_sidebar( 'before-footer' ) ) : ?>
<aside class='before-footer'>
    <?php dynamic_sidebar( 'before-footer' ); ?>
</aside>
<?php endif; ?>

<footer uk-height-viewport='expand: true' class='uk-section-small' role='contentinfo'>
    <div class='uk-container'>
        <div class='uk-grid' uk-grid>
            <?php get_template_part('partials/footer','widget'); ?>
            <div class='uk-flex-first@m uk-width-expand'>
                <?php get_template_part('partials/footer', 'logo', 'uk-display-block uk-margin-medium-bottom'); ?>
                <?php the_contact_phone('call', 0, 'uk-text-emphasis'); ?>
                <?php get_template_part('partials/footer', 'menu', 'uk-margin-small-top uk-text-small uk-grid-row-collapse uk-grid-small uk-grid-divider'); ?>
                <div class='uk-grid uk-grid-small uk-flex-middle' uk-grid style='font-size: 0.625em; margin-top: 4px'>
                    <?php get_template_part('partials/copyright', 'menu', 'uk-grid-row-collapse uk-grid-xsmall uk-grid-divider'); ?>
                    <?php get_template_part('partials/copyright', '', 'uk-margin-remove uk-link-text'); ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
