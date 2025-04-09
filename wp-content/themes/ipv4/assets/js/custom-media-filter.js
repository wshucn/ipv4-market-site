(function () {
  /**
   * Create a new MediaLibraryTaxonomyFilter we later will instantiate
   */
  var MediaLibraryTaxonomyFilter = wp.media.view.AttachmentFilters.extend({
    id: 'media-attachment-taxonomy-filter',
    createFilters: function () {
      var filters = {};
      // Formats the 'terms' we've included via wp_localize_script()
      // console.log(MediaLibraryTaxonomyFilterData.terms);
      let terms = MediaLibraryTaxonomyFilterData.terms || {};
      _.each(terms || {}, function (value, index) {
        filters[value.slug] = {
          text: value.name,
          props: {
            // Change this: key needs to be the WP_Query var for the taxonomy
            tag: value.slug
          }
        };
      });
      filters.all = {
        // Change this: use whatever default label you'd like
        text: 'All tags',
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
    createToolbar: function () {
      // Make sure to load the original toolbar
      AttachmentsBrowser.prototype.createToolbar.call(this);
      this.toolbar.set('MediaLibraryTaxonomyFilterLabel', new wp.media.view.Label({
        value: 'Filter by Tag',
        attributes: {
          'for': 'media-attachment-taxonomy-filter'
        },
        priority: -75
      }).render());
      this.toolbar.set('MediaLibraryTaxonomyFilter', new MediaLibraryTaxonomyFilter({
        controller: this.controller,
        model: this.collection.props,
        priority: -75
      }).render());
    }
  });
})();//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiY3VzdG9tLW1lZGlhLWZpbHRlci5qcyIsIm5hbWVzIjpbIk1lZGlhTGlicmFyeVRheG9ub215RmlsdGVyIiwid3AiLCJtZWRpYSIsInZpZXciLCJBdHRhY2htZW50RmlsdGVycyIsImV4dGVuZCIsImlkIiwiY3JlYXRlRmlsdGVycyIsImZpbHRlcnMiLCJ0ZXJtcyIsIk1lZGlhTGlicmFyeVRheG9ub215RmlsdGVyRGF0YSIsIl8iLCJlYWNoIiwidmFsdWUiLCJpbmRleCIsInNsdWciLCJ0ZXh0IiwibmFtZSIsInByb3BzIiwidGFnIiwiYWxsIiwicHJpb3JpdHkiLCJBdHRhY2htZW50c0Jyb3dzZXIiLCJjcmVhdGVUb29sYmFyIiwicHJvdG90eXBlIiwiY2FsbCIsInRvb2xiYXIiLCJzZXQiLCJMYWJlbCIsImF0dHJpYnV0ZXMiLCJyZW5kZXIiLCJjb250cm9sbGVyIiwibW9kZWwiLCJjb2xsZWN0aW9uIl0sInNvdXJjZXMiOlsiY3VzdG9tLW1lZGlhLWZpbHRlci5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gKCkge1xuICAvKipcbiAgICogQ3JlYXRlIGEgbmV3IE1lZGlhTGlicmFyeVRheG9ub215RmlsdGVyIHdlIGxhdGVyIHdpbGwgaW5zdGFudGlhdGVcbiAgICovXG4gIHZhciBNZWRpYUxpYnJhcnlUYXhvbm9teUZpbHRlciA9IHdwLm1lZGlhLnZpZXcuQXR0YWNobWVudEZpbHRlcnMuZXh0ZW5kKHtcbiAgICBpZDogJ21lZGlhLWF0dGFjaG1lbnQtdGF4b25vbXktZmlsdGVyJyxcbiAgICBjcmVhdGVGaWx0ZXJzOiBmdW5jdGlvbiAoKSB7XG4gICAgICB2YXIgZmlsdGVycyA9IHt9O1xuICAgICAgLy8gRm9ybWF0cyB0aGUgJ3Rlcm1zJyB3ZSd2ZSBpbmNsdWRlZCB2aWEgd3BfbG9jYWxpemVfc2NyaXB0KClcbiAgICAgIC8vIGNvbnNvbGUubG9nKE1lZGlhTGlicmFyeVRheG9ub215RmlsdGVyRGF0YS50ZXJtcyk7XG4gICAgICBsZXQgdGVybXMgPSBNZWRpYUxpYnJhcnlUYXhvbm9teUZpbHRlckRhdGEudGVybXMgfHwge307XG4gICAgICBfLmVhY2godGVybXMgfHwge30sIGZ1bmN0aW9uICh2YWx1ZSwgaW5kZXgpIHtcbiAgICAgICAgZmlsdGVyc1t2YWx1ZS5zbHVnXSA9IHtcbiAgICAgICAgICB0ZXh0OiB2YWx1ZS5uYW1lLFxuICAgICAgICAgIHByb3BzOiB7XG4gICAgICAgICAgICAvLyBDaGFuZ2UgdGhpczoga2V5IG5lZWRzIHRvIGJlIHRoZSBXUF9RdWVyeSB2YXIgZm9yIHRoZSB0YXhvbm9teVxuICAgICAgICAgICAgdGFnOiB2YWx1ZS5zbHVnXG4gICAgICAgICAgfVxuICAgICAgICB9O1xuICAgICAgfSk7XG4gICAgICBmaWx0ZXJzLmFsbCA9IHtcbiAgICAgICAgLy8gQ2hhbmdlIHRoaXM6IHVzZSB3aGF0ZXZlciBkZWZhdWx0IGxhYmVsIHlvdSdkIGxpa2VcbiAgICAgICAgdGV4dDogJ0FsbCB0YWdzJyxcbiAgICAgICAgcHJvcHM6IHtcbiAgICAgICAgICAvLyBDaGFuZ2UgdGhpczoga2V5IG5lZWRzIHRvIGJlIHRoZSBXUF9RdWVyeSB2YXIgZm9yIHRoZSB0YXhvbm9teVxuICAgICAgICAgIHRhZzogJydcbiAgICAgICAgfSxcbiAgICAgICAgcHJpb3JpdHk6IDEwXG4gICAgICB9O1xuICAgICAgdGhpcy5maWx0ZXJzID0gZmlsdGVycztcbiAgICB9XG4gIH0pO1xuICAvKipcbiAgICogRXh0ZW5kIGFuZCBvdmVycmlkZSB3cC5tZWRpYS52aWV3LkF0dGFjaG1lbnRzQnJvd3NlciB0byBpbmNsdWRlIG91ciBuZXcgZmlsdGVyXG4gICAqL1xuICB2YXIgQXR0YWNobWVudHNCcm93c2VyID0gd3AubWVkaWEudmlldy5BdHRhY2htZW50c0Jyb3dzZXI7XG4gIHdwLm1lZGlhLnZpZXcuQXR0YWNobWVudHNCcm93c2VyID0gd3AubWVkaWEudmlldy5BdHRhY2htZW50c0Jyb3dzZXIuZXh0ZW5kKHtcbiAgICBjcmVhdGVUb29sYmFyOiBmdW5jdGlvbiAoKSB7XG4gICAgICAvLyBNYWtlIHN1cmUgdG8gbG9hZCB0aGUgb3JpZ2luYWwgdG9vbGJhclxuICAgICAgQXR0YWNobWVudHNCcm93c2VyLnByb3RvdHlwZS5jcmVhdGVUb29sYmFyLmNhbGwodGhpcyk7XG4gICAgICB0aGlzLnRvb2xiYXIuc2V0KCdNZWRpYUxpYnJhcnlUYXhvbm9teUZpbHRlckxhYmVsJywgbmV3IHdwLm1lZGlhLnZpZXcuTGFiZWwoe1xuICAgICAgICB2YWx1ZTogJ0ZpbHRlciBieSBUYWcnLFxuICAgICAgICBhdHRyaWJ1dGVzOiB7XG4gICAgICAgICAgJ2Zvcic6ICdtZWRpYS1hdHRhY2htZW50LXRheG9ub215LWZpbHRlcidcbiAgICAgICAgfSxcbiAgICAgICAgcHJpb3JpdHk6IC03NVxuICAgICAgfSkucmVuZGVyKCkpO1xuICAgICAgdGhpcy50b29sYmFyLnNldCgnTWVkaWFMaWJyYXJ5VGF4b25vbXlGaWx0ZXInLCBuZXcgTWVkaWFMaWJyYXJ5VGF4b25vbXlGaWx0ZXIoe1xuICAgICAgICBjb250cm9sbGVyOiB0aGlzLmNvbnRyb2xsZXIsXG4gICAgICAgIG1vZGVsOiB0aGlzLmNvbGxlY3Rpb24ucHJvcHMsXG4gICAgICAgIHByaW9yaXR5OiAtNzVcbiAgICAgIH0pLnJlbmRlcigpKTtcbiAgICB9XG4gIH0pO1xufSkoKTtcbiJdLCJtYXBwaW5ncyI6IkFBQUEsQ0FBQyxZQUFZO0VBQ1g7QUFDRjtBQUNBO0VBQ0UsSUFBSUEsMEJBQTBCLEdBQUdDLEVBQUUsQ0FBQ0MsS0FBSyxDQUFDQyxJQUFJLENBQUNDLGlCQUFpQixDQUFDQyxNQUFNLENBQUM7SUFDdEVDLEVBQUUsRUFBRSxrQ0FBa0M7SUFDdENDLGFBQWEsRUFBRSxTQUFBQSxDQUFBLEVBQVk7TUFDekIsSUFBSUMsT0FBTyxHQUFHLENBQUMsQ0FBQztNQUNoQjtNQUNBO01BQ0EsSUFBSUMsS0FBSyxHQUFHQyw4QkFBOEIsQ0FBQ0QsS0FBSyxJQUFJLENBQUMsQ0FBQztNQUN0REUsQ0FBQyxDQUFDQyxJQUFJLENBQUNILEtBQUssSUFBSSxDQUFDLENBQUMsRUFBRSxVQUFVSSxLQUFLLEVBQUVDLEtBQUssRUFBRTtRQUMxQ04sT0FBTyxDQUFDSyxLQUFLLENBQUNFLElBQUksQ0FBQyxHQUFHO1VBQ3BCQyxJQUFJLEVBQUVILEtBQUssQ0FBQ0ksSUFBSTtVQUNoQkMsS0FBSyxFQUFFO1lBQ0w7WUFDQUMsR0FBRyxFQUFFTixLQUFLLENBQUNFO1VBQ2I7UUFDRixDQUFDO01BQ0gsQ0FBQyxDQUFDO01BQ0ZQLE9BQU8sQ0FBQ1ksR0FBRyxHQUFHO1FBQ1o7UUFDQUosSUFBSSxFQUFFLFVBQVU7UUFDaEJFLEtBQUssRUFBRTtVQUNMO1VBQ0FDLEdBQUcsRUFBRTtRQUNQLENBQUM7UUFDREUsUUFBUSxFQUFFO01BQ1osQ0FBQztNQUNELElBQUksQ0FBQ2IsT0FBTyxHQUFHQSxPQUFPO0lBQ3hCO0VBQ0YsQ0FBQyxDQUFDO0VBQ0Y7QUFDRjtBQUNBO0VBQ0UsSUFBSWMsa0JBQWtCLEdBQUdyQixFQUFFLENBQUNDLEtBQUssQ0FBQ0MsSUFBSSxDQUFDbUIsa0JBQWtCO0VBQ3pEckIsRUFBRSxDQUFDQyxLQUFLLENBQUNDLElBQUksQ0FBQ21CLGtCQUFrQixHQUFHckIsRUFBRSxDQUFDQyxLQUFLLENBQUNDLElBQUksQ0FBQ21CLGtCQUFrQixDQUFDakIsTUFBTSxDQUFDO0lBQ3pFa0IsYUFBYSxFQUFFLFNBQUFBLENBQUEsRUFBWTtNQUN6QjtNQUNBRCxrQkFBa0IsQ0FBQ0UsU0FBUyxDQUFDRCxhQUFhLENBQUNFLElBQUksQ0FBQyxJQUFJLENBQUM7TUFDckQsSUFBSSxDQUFDQyxPQUFPLENBQUNDLEdBQUcsQ0FBQyxpQ0FBaUMsRUFBRSxJQUFJMUIsRUFBRSxDQUFDQyxLQUFLLENBQUNDLElBQUksQ0FBQ3lCLEtBQUssQ0FBQztRQUMxRWYsS0FBSyxFQUFFLGVBQWU7UUFDdEJnQixVQUFVLEVBQUU7VUFDVixLQUFLLEVBQUU7UUFDVCxDQUFDO1FBQ0RSLFFBQVEsRUFBRSxDQUFDO01BQ2IsQ0FBQyxDQUFDLENBQUNTLE1BQU0sQ0FBQyxDQUFDLENBQUM7TUFDWixJQUFJLENBQUNKLE9BQU8sQ0FBQ0MsR0FBRyxDQUFDLDRCQUE0QixFQUFFLElBQUkzQiwwQkFBMEIsQ0FBQztRQUM1RStCLFVBQVUsRUFBRSxJQUFJLENBQUNBLFVBQVU7UUFDM0JDLEtBQUssRUFBRSxJQUFJLENBQUNDLFVBQVUsQ0FBQ2YsS0FBSztRQUM1QkcsUUFBUSxFQUFFLENBQUM7TUFDYixDQUFDLENBQUMsQ0FBQ1MsTUFBTSxDQUFDLENBQUMsQ0FBQztJQUNkO0VBQ0YsQ0FBQyxDQUFDO0FBQ0osQ0FBQyxFQUFFLENBQUMiLCJpZ25vcmVMaXN0IjpbXX0=
