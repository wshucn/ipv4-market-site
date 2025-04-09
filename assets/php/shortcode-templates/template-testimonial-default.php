<div class="uk-position-relative uk-margin-top sect-testimonials">
    <div class="uk-container uk-container-large">
        <div class="uk-position-relative" uk-slider="autoplay: true;autoplay-interval:4000">
            <ul class="uk-slider-items">
                {items}
                <li class="uk-margin-remove uk-width-1-1" aria-roledescription="slide">
                    <div>
                        <div class="uk-text-center">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </li>
                {/items}
            </ul>

            <a class="uk-position-center-left uk-position-small" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
            <a class="uk-position-center-right uk-position-small" href="#" uk-slidenav-next uk-slider-item="next"></a>

            <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
        </div>
    </div>
</div>
