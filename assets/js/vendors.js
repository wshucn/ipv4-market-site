"use strict";

wp.domReady(() => {
  wp.blocks.registerBlockVariation('core/gallery', [{
    name: 'lightbox-fade',
    title: 'Lightbox (Fade)',
    attributes: {
      className: 'uk-lightbox-fade',
      linkTo: 'file',
      sizeSlug: 'full'
    },
    scope: ['inserter', 'transform']
  }, {
    name: 'lightbox-slide',
    title: 'Lightbox (Slide)',
    attributes: {
      className: 'uk-lightbox-slide',
      linkTo: 'file',
      sizeSlug: 'full'
    },
    scope: ['inserter', 'transform']
  }, {
    name: 'lightbox-scale',
    title: 'Lightbox (Scale)',
    attributes: {
      className: 'uk-lightbox-scale',
      linkTo: 'file',
      sizeSlug: 'full'
    },
    scope: ['inserter', 'transform']
  }]);
  wp.blocks.registerBlockStyle('core/image', [{
    name: 'cropped',
    label: 'Cropped to Fit'
  }, {
    name: 'square',
    label: 'Square'
  }]);
  wp.blocks.registerBlockStyle('core/media-text', [{
    name: 'autoheight',
    label: 'Auto Height'
  }]);
  wp.blocks.registerBlockStyle('core/heading', [{
    name: 'alt',
    label: 'Heading (Alternate)'
  }, {
    name: 'divider',
    label: 'Heading (Divider)'
  }]);
  wp.blocks.registerBlockStyle('core/gallery', [{
    name: 'slider',
    label: 'Slider w/ Arrows only'
  }, {
    name: 'thumbnav',
    label: 'Slider w/ Thumb Navigation'
  }, {
    name: 'dotnav',
    label: 'Slider w/ Dot Navigation'
  }]);
  wp.blocks.registerBlockStyle('core/columns', {
    name: 'height-matched',
    label: 'Equal Height'
  });
  wp.blocks.registerBlockStyle('core/list', [{
    name: 'theme',
    label: 'Theme Style'
  }, {
    name: 'none',
    label: 'None'
  }, {
    name: 'circle',
    label: 'Circle'
  }, {
    name: 'square',
    label: 'Square'
  }, {
    name: 'hyphen',
    label: 'Hyphen'
  }]);
  wp.blocks.registerBlockStyle('core/separator', {
    name: 'icon',
    label: 'Separator w/ Icon'
  });
  wp.blocks.registerBlockVariation('core/columns', {
    name: 'four-columns',
    title: '25 / 25 / 25 / 25',
    description: 'Four columns; equal split',
    scope: ['block'],
    innerBlocks: [['core/column'], ['core/column'], ['core/column'], ['core/column']]
  });
  wp.blocks.registerBlockStyle('core/group', [{
    name: 'container-small',
    label: 'Small Width'
  }, {
    name: 'container-medium',
    label: 'Medium Width'
  }, {
    name: 'container-large',
    label: 'Large Width'
  }, {
    name: 'container-xlarge',
    label: 'Extra Large Width'
  }]);
  wp.blocks.registerBlockVariation('core/group', [{
    name: 'section',
    title: 'Section',
    attributes: {
      className: 'uk-section',
      tagName: 'section'
    }
  }, {
    name: 'section-large',
    title: 'Section (Large)',
    attributes: {
      className: 'uk-section uk-section-large',
      tagName: 'section'
    }
  }, {
    name: 'tabs',
    title: 'Tabs',
    attributes: {
      className: 'tabs',
      tagName: 'div'
    }
  }, {
    name: 'switcher',
    title: 'Switcher',
    attributes: {
      className: 'switcher'
    }
  }]);
});
"use strict";

function addFlexAttribute(settings, name) {
  if (typeof settings.attributes !== 'undefined') {
    if (name == 'core/columns') {
      settings.attributes = Object.assign(settings.attributes, {
        useFlex: {
          type: 'boolean'
        }
      });
    }
  }
  return settings;
}
wp.hooks.addFilter('blocks.registerBlockType', 'mp/flex-custom-attribute', addFlexAttribute);
const columnsControls = wp.compose.createHigherOrderComponent(BlockEdit => {
  return props => {
    const {
      Fragment
    } = wp.element;
    const {
      ToggleControl,
      PanelBody
    } = wp.components;
    const {
      InspectorControls
    } = wp.blockEditor;
    const {
      attributes,
      setAttributes,
      isSelected
    } = props;
    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), isSelected && props.name == 'core/columns' && /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, null, /*#__PURE__*/React.createElement(ToggleControl, {
      label: wp.i18n.__('Use Flex', 'mp'),
      checked: !!attributes.useFlex,
      onChange: newval => setAttributes({
        useFlex: !attributes.useFlex
      })
    }))));
  };
}, 'columnsControls');
wp.hooks.addFilter('editor.BlockEdit', 'mp/columns-control', columnsControls);
function columnsApplyExtraClass(extraProps, blockType, attributes) {
  const {
    useFlex
  } = attributes;
  if (typeof useFlex !== 'undefined' && useFlex) {
    extraProps.className = extraProps.className + ' uk-flex uk-flex-wrap';
  }
  return extraProps;
}
wp.hooks.addFilter('blocks.getSaveContent.extraProps', 'mp/columns-apply-class', columnsApplyExtraClass);
function addSingleAttribute(settings, name) {
  if (typeof settings.attributes !== 'undefined') {
    if (name == 'core/list') {
      settings.attributes = Object.assign(settings.attributes, {
        useSingle: {
          type: 'boolean'
        }
      });
    }
  }
  return settings;
}
wp.hooks.addFilter('blocks.registerBlockType', 'mp/single-custom-attribute', addSingleAttribute);
const listsControls = wp.compose.createHigherOrderComponent(BlockEdit => {
  return props => {
    const {
      Fragment
    } = wp.element;
    const {
      ToggleControl,
      PanelBody
    } = wp.components;
    const {
      InspectorControls
    } = wp.blockEditor;
    const {
      attributes,
      setAttributes,
      isSelected
    } = props;
    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), isSelected && props.name == 'core/list' && /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, null, /*#__PURE__*/React.createElement(ToggleControl, {
      label: wp.i18n.__('Single Space', 'mp'),
      checked: !!attributes.useSingle,
      onChange: newval => setAttributes({
        useSingle: !attributes.useSingle
      })
    }))));
  };
}, 'listsControls');
wp.hooks.addFilter('editor.BlockEdit', 'mp/lists-control', listsControls);
function listsApplyExtraClass(extraProps, blockType, attributes) {
  const {
    useSingle
  } = attributes;
  if (typeof useSingle !== 'undefined' && useSingle) {
    extraProps.className = extraProps.className + ' single-space';
  }
  return extraProps;
}
wp.hooks.addFilter('blocks.getSaveContent.extraProps', 'mp/lists-apply-class', listsApplyExtraClass);