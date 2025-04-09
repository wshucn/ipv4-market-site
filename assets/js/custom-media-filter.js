(function () {
	/**
	 * Create a new MediaLibraryTaxonomyFilter we later will instantiate
	 */
	var MediaLibraryTaxonomyFilter = wp.media.view.AttachmentFilters.extend({
		id: 'media-attachment-taxonomy-filter',

		createFilters: function() {
			var filters = {};
			// Formats the 'terms' we've included via wp_localize_script()
			// console.log(MediaLibraryTaxonomyFilterData.terms);
			let terms = MediaLibraryTaxonomyFilterData.terms || {};

			_.each( terms || {}, function( value, index ) {
				filters[ value.slug ] = {
					text: value.name,
					props: {
						// Change this: key needs to be the WP_Query var for the taxonomy
						tag: value.slug,
					}
				};
			});

			filters.all = {
				// Change this: use whatever default label you'd like
				text:  'All tags',
				props: {
					// Change this: key needs to be the WP_Query var for the taxonomy
					tag: ''
				},
				priority: 10
			};
			this.filters = filters;
		}
	});
	/**
	 * Extend and override wp.media.view.AttachmentsBrowser to include our new filter
	 */
	var AttachmentsBrowser = wp.media.view.AttachmentsBrowser;
	wp.media.view.AttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend({
		createToolbar: function() {
			// Make sure to load the original toolbar
			AttachmentsBrowser.prototype.createToolbar.call(this);
			this.toolbar.set( 'MediaLibraryTaxonomyFilterLabel', new wp.media.view.Label({
                value: 'Filter by Tag',
                attributes: {
                    'for': 'media-attachment-taxonomy-filter'
                },
                priority: -75
            }).render() );
			this.toolbar.set( 'MediaLibraryTaxonomyFilter', new MediaLibraryTaxonomyFilter({
				controller: this.controller,
				model:      this.collection.props,
				priority: -75
			}).render() );
		}
	});
})()