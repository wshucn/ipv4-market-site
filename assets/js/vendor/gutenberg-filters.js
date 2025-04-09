function addFlexAttribute(settings, name) {
	if (typeof settings.attributes !== 'undefined') {
		if (name == 'core/columns') {
			settings.attributes = Object.assign(settings.attributes, {
				useFlex: {
					type: 'boolean',
				}
			});
		}
	}
	return settings;
}

wp.hooks.addFilter(
	'blocks.registerBlockType',
	'mp/flex-custom-attribute',
	addFlexAttribute
);

const columnsControls = wp.compose.createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { Fragment } = wp.element;
		const { ToggleControl, PanelBody } = wp.components;
		const { InspectorControls } = wp.blockEditor;
		const { attributes, setAttributes, isSelected } = props;
		return (
			<Fragment>
				<BlockEdit {...props} />
				{isSelected && (props.name == 'core/columns') &&
					<InspectorControls>
						<PanelBody>
							<ToggleControl
								label={wp.i18n.__('Use Flex', 'mp')}
								checked={!!attributes.useFlex}
								onChange={(newval) => setAttributes({ useFlex: !attributes.useFlex })}
							/>
						</PanelBody>
					</InspectorControls>
				}
			</Fragment>
		);
	};
}, 'columnsControls');

wp.hooks.addFilter(
	'editor.BlockEdit',
	'mp/columns-control',
	columnsControls
);

function columnsApplyExtraClass(extraProps, blockType, attributes) {
	const { useFlex } = attributes;

	if (typeof useFlex !== 'undefined' && useFlex) {
		extraProps.className = extraProps.className + ' uk-flex uk-flex-wrap';
	}
	return extraProps;
}

wp.hooks.addFilter(
	'blocks.getSaveContent.extraProps',
	'mp/columns-apply-class',
	columnsApplyExtraClass
);


function addSingleAttribute(settings, name) {
	if (typeof settings.attributes !== 'undefined') {
		if (name == 'core/list') {
			settings.attributes = Object.assign(settings.attributes, {
				useSingle: {
					type: 'boolean',
				}
			});
		}
	}
	return settings;
}

wp.hooks.addFilter(
	'blocks.registerBlockType',
	'mp/single-custom-attribute',
	addSingleAttribute
);

const listsControls = wp.compose.createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { Fragment } = wp.element;
		const { ToggleControl, PanelBody } = wp.components;
		const { InspectorControls } = wp.blockEditor;
		const { attributes, setAttributes, isSelected } = props;
		return (
			<Fragment>
				<BlockEdit {...props} />
				{isSelected && (props.name == 'core/list') &&
					<InspectorControls>
						<PanelBody>
							<ToggleControl
								label={wp.i18n.__('Single Space', 'mp')}
								checked={!!attributes.useSingle}
								onChange={(newval) => setAttributes({ useSingle: !attributes.useSingle })}
							/>
						</PanelBody>
					</InspectorControls>
				}
			</Fragment>
		);
	};
}, 'listsControls');

wp.hooks.addFilter(
	'editor.BlockEdit',
	'mp/lists-control',
	listsControls
);

function listsApplyExtraClass(extraProps, blockType, attributes) {
	const { useSingle } = attributes;

	if (typeof useSingle !== 'undefined' && useSingle) {
		extraProps.className = extraProps.className + ' single-space';
	}
	return extraProps;
}

wp.hooks.addFilter(
	'blocks.getSaveContent.extraProps',
	'mp/lists-apply-class',
	listsApplyExtraClass
);
