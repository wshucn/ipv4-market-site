

wp.domReady( () => {
 
    wp.blocks.registerBlockVariation(
        'core/gallery',
        [
            {
                name: 'lightbox-fade',
                title: 'Lightbox (Fade)',
                attributes: {
                    className: 'uk-lightbox-fade',
                    linkTo: 'file',
                    sizeSlug: 'full',
                },
                scope: [ 'inserter', 'transform' ],
            },
            {
                name: 'lightbox-slide',
                title: 'Lightbox (Slide)',
                attributes: {
                    className: 'uk-lightbox-slide',
                    linkTo: 'file',
                    sizeSlug: 'full',
                },
                scope: [ 'inserter', 'transform' ],
            },
            {
                name: 'lightbox-scale',
                title: 'Lightbox (Scale)',
                attributes: {
                    className: 'uk-lightbox-scale',
                    linkTo: 'file',
                    sizeSlug: 'full',
                },
                scope: [ 'inserter', 'transform' ],
            },
        ]
    );

    wp.blocks.registerBlockStyle(
        'core/image',
        [
            {
                name: 'cropped',
                label: 'Cropped to Fit',
            },
            {
                name: 'square',
                label: 'Square',
            },
        ]
    );

    wp.blocks.registerBlockStyle(
        'core/media-text',
        [
            {
                name: 'autoheight',
                label: 'Auto Height',
            },
        ]
    );

    wp.blocks.registerBlockStyle(
        'core/heading',
        [
            {
                name: 'alt',
                label: 'Heading (Alternate)',
            },
            {
                name: 'divider',
                label: 'Heading (Divider)',
            },
        ]
    );

    wp.blocks.registerBlockStyle(
        'core/gallery',
        [
            {
                name: 'slider',
                label: 'Slider w/ Arrows only',
            },
            {
                name: 'thumbnav',
                label: 'Slider w/ Thumb Navigation',
            },
            {
                name: 'dotnav',
                label: 'Slider w/ Dot Navigation',
            },
        ]
    );

    wp.blocks.registerBlockStyle(
        'core/columns',
        {
            name: 'height-matched',
            label: 'Equal Height',
        },
    );

    wp.blocks.registerBlockStyle(
        'core/list',
        [
            {
                name: 'theme',
                label: 'Theme Style',
            },
            {
                name: 'none',
                label: 'None',
            },
            {
                name: 'circle',
                label: 'Circle',
            },
            {
                name: 'square',
                label: 'Square',
            },
            {
                name: 'hyphen',
                label: 'Hyphen',
            },
        ]
    );
    
    wp.blocks.registerBlockStyle(
        'core/separator',
        {
            name: 'icon',
            label: 'Separator w/ Icon',
        },
    );


    wp.blocks.registerBlockVariation(
        'core/columns', {
          name: 'four-columns',
          title: '25 / 25 / 25 / 25',
          description: 'Four columns; equal split',
          scope: ['block'],
          innerBlocks: [
            ['core/column'],
            ['core/column'],
            ['core/column'],
            ['core/column'],
          ],
        }
    );

    wp.blocks.registerBlockStyle(
        'core/group',
        [
            {
                name: 'container-small',
                label: 'Small Width'
            },
            {
                name: 'container-medium',
                label: 'Medium Width'
            },
            {
                name: 'container-large',
                label: 'Large Width'
            },
            {
                name: 'container-xlarge',
                label: 'Extra Large Width'
            }
        ]
    );

    wp.blocks.registerBlockVariation(
        'core/group',
        [
            {
                name: 'section',
                title: 'Section',
                attributes: {
                    className: 'uk-section',
                    tagName: 'section'
                },
            },
            {
                name: 'section-large',
                title: 'Section (Large)',
                attributes: {
                    className: 'uk-section uk-section-large',
                    tagName: 'section'
                },
            },
            {
                name: 'tabs',
                title: 'Tabs',
                attributes: {
                    className: 'tabs',
                    tagName: 'div'
                },
            },
            {
                name: 'switcher',
                title: 'Switcher',
                attributes: {
                    className: 'switcher',
                },
            },
        ]
    );
} );