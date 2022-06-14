// Handle block(s) variations.
wp.blocks.registerBlockVariation('core/heading', {
	icon: 'editor-bold',
	name: 'core/strong-heading',
	title: wp.i18n._x('Strong title', 'Block Editor', 'energica'),
	attributes: {
		className: 'strong-title text-uppercase',
	},
});
wp.blocks.registerBlockVariation('core/list', {
	icon: 'menu-alt',
	name: 'core/unordered-list',
	title: wp.i18n._x('Unordered list', 'Block Editor', 'energica'),
	description: wp.i18n._x('Create unordered list.', 'Block Editor', 'energica'),
	attributes: {
		className: 'list-unstyled',
	},
});

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
