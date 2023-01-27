// Handle block(s) variations.
wp.blocks.registerBlockVariation('core/heading', {
	icon: 'editor-bold',
	name: 'core/strong-heading',
	title: wp.i18n._x('Strong title', 'Block Editor', 'gioia'),
	attributes: {
		className: 'strong-title',
	},
});
wp.blocks.registerBlockVariation('core/list', {
	icon: 'menu-alt',
	name: 'core/unordered-list',
	title: wp.i18n._x('Unordered list', 'Block Editor', 'gioia'),
	description: wp.i18n._x('Create unordered list.', 'Block Editor', 'gioia'),
	attributes: {
		className: 'list-unstyled',
	},
});

// Handle block(s) settings.
function coreBlocksAttributesHandler(settings, name) {
	if (name === 'core/button') {
		settings.attributes = Object.assign(settings.attributes, {
			icon: {
				type: 'string',
				default: '',
			},
			icon_pos: {
				type: 'string',
				default: 'left',
			},
		});
	}
	return settings;
}
wp.hooks.addFilter('blocks.registerBlockType', 'gioia/core-blocks/attributes', coreBlocksAttributesHandler);

/**
 * Create HOC to add icon SelectControl to inspector controls of the button block.
 */
var el = wp.element.createElement;
var Fragment = wp.element.Fragment;
var withIconControl = wp.compose.createHigherOrderComponent(function (BlockEdit) {
	return function (props) {
		if (props.hasOwnProperty('name') && props.name === 'core/button') {
			var icon = props.attributes.icon || '';
			var iconPos = props.attributes.icon_pos || '';
			function onIconChange(val) {
				props.setAttributes({ icon: val });
			}
			function onIconPositionChange(val) {
				props.setAttributes({ icon_pos: val });
			}
			var iconOptions = [{ label: '---', value: '' }];
			if (typeof gioia_icons !== 'undefined') {
				Object.keys(gioia_icons).forEach(function (name) {
					iconOptions.push({ label: gioia_icons[name].label, value: name });
				});
			}
			return el(
				Fragment,
				{},
				el(BlockEdit, props),
				el(
					wp.blockEditor.InspectorControls,
					{},
					el(
						wp.components.PanelBody,
						{ title: wp.i18n._x('Additional settings', 'Block Editor', 'gioia'), initialOpen: false },
						el(wp.components.SelectControl, {
							label: wp.i18n._x('Icon', 'Block Editor', 'gioia'),
							options: iconOptions,
							onChange: onIconChange,
							value: icon,
						}),
						icon
							? el(wp.components.SelectControl, {
									label: wp.i18n._x('Position', 'Block Editor', 'gioia'),
									options: [
										{
											label: wp.i18n._x('Left', 'Block Editor', 'gioia'),
											value: 'left',
										},
										{
											label: wp.i18n._x('Right', 'Block Editor', 'gioia'),
											value: 'right',
										},
									],
									onChange: onIconPositionChange,
									value: iconPos,
							  })
							: ''
					)
				)
			);
		}
		return el(BlockEdit, props);
	};
}, 'withIconControl');
wp.hooks.addFilter('editor.BlockEdit', 'gioia/core-blocks/button', withIconControl, 110);

/**
 * Handle Block Editor unregistration (blocks or styles).
 *
 * @link https://wordpress.org/support/article/blocks/
 */
wp.domReady(function () {
	var unwantedBlocks = [
		'core/archives',
		'core/calendar',
		'core/code',
		'core/freeform',
		'core/preformatted',
		'core/audio',
		'core/verse',
		'core/more',
		'core/nextpage',
		'core/latest-comments',
		'core/loginout',
		'core/page-list',
	];
	var unwantedEmbeds = [
		'wordpress',
		'soundcloud',
		'animoto',
		'cloudup',
		'crowdsignal',
		'dailymotion',
		'hulu',
		'imgur',
		'issuu',
		'kickstarter',
		'meetup-com',
		'mixcloud',
		'reddit',
		'reverbnation',
		'screencast',
		'scribd',
		'smugmug',
		'speaker-deck',
		'ted',
		'tumblr',
		'videopress',
		'wordpress-tv',
		'amazon-kindle',
		'wolfram-cloud',
	];
	wp.blocks.getBlockTypes().forEach(function (blockType) {
		if (unwantedBlocks.indexOf(blockType.name) !== -1) {
			wp.blocks.unregisterBlockType(blockType.name);
		}
		if (blockType.name.indexOf('yoast/') !== -1 || blockType.name.indexOf('yoast-seo/') !== -1) {
			wp.blocks.unregisterBlockType(blockType.name);
		}
		if (blockType.name.indexOf('contact-form-7/') !== -1) {
			wp.blocks.unregisterBlockType(blockType.name);
		}
		if (blockType.name.indexOf('polylang/') !== -1) {
			wp.blocks.unregisterBlockType(blockType.name);
		}
	});
	for (var i = unwantedEmbeds.length - 1; i >= 0; i--) {
		wp.blocks.unregisterBlockVariation('core/embed', unwantedEmbeds[i]);
	}
	wp.blocks.unregisterBlockStyle('core/image', 'default');
	wp.blocks.unregisterBlockStyle('core/image', 'rounded');
});
