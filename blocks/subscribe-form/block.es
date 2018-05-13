'use strict';

import ListSelect from './components/list-select.es';
import FormView from './components/form-view.es';

const wp = window.wp || {};

const { setLocaleData, __ } = wp.i18n;
const { registerBlockType, BlockControls } = wp.blocks;
const { createElement: el } = wp.element;
const { Toolbar } = wp.components;
const { RichText } = wp.blocks;

/**
 * Creates a new Jed instance with specified locale data configuration for the plugin.
 *
 * @see https://www.npmjs.com/package/@wordpress/i18n
 */
setLocaleData( wpChimpLocaleConfigs, 'wp-chimp' );

/**
 *
 */
const TOOLBAR_CONTROLS = [
  {
    icon: 'visibility',
    title: __( 'Preview the Form', 'wp-chimp' ),
    controlView: 'form-preview'
  },
  {
    icon: 'index-card',
    title: __( 'Select a List', 'wp-chimp' ),
    controlView: 'select-list'
  }
];

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://wordpress.org/gutenberg/handbook/block-api/
 */
registerBlockType( 'wp-chimp/subscribe-form', {

  /**
   * This is the display title for your block, which can be translated with `i18n` functions.
   * The block inserter will show this name.
   */
  title: __( 'MailChimp Form', 'wp-chimp' ),

  /**
   * The icon shown in the Gutenberg block list.
   *
   * @see https://developer.wordpress.org/resource/dashicons/
   *
   * @type {String}
   */
  icon: 'feedback',

  /**
   * Blocks are grouped into categories to help users browse and discover them.
   * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
   */
  category: 'widgets',

  /**
   * Optional block extended support features.
   */
  supports: {
    html: true // Removes support for an HTML mode.
  },

  /**
   * Make it easier to discover a block with keyword aliases
   *
   * @type {Array}
   */
  keywords: [ 'form', 'subcribe' ],

  /**
   * Blocks attributes, their type, and the default value.
   *
   * @type {Object}
   */
	attributes: {
    listId: {
      type: 'string'
    },
		headingText: {
      type: 'string',
      default: __( 'Subscribe to our newsletter', 'wp-chimp' )
    },
		subHeadingText: {
      type: 'string',
      default: __( 'Get notified of our next update right to your inbox', 'wp-chimp' )
    },
		inputEmailPlaceholder: {
      type: 'string',
      default: __( 'Enter your email address', 'wp-chimp' )
    },
		buttonText: {
      type: 'string',
      default: __( 'Subscribe', 'wp-chimp' )
    }
	},

  /**
   * The edit function describes the structure of your block in the context of the editor.
   * This represents what the editor will render when the block is used.
   *
   * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
   *
   * @param {Object} [props] Properties passed from the editor.
   * @return {Element}       Element to render.
   */
  edit( props ) {
    const { className, attributes, setAttributes } = props;
    return [
      el( BlockControls, {
        key: 'form-controls',
        className: `${className}__block-controls`
      }, el( ListSelect, props ) ),
      el( FormView, props )
    ];
  },

  /**
   * The save function defines the way in which the different attributes should be combined
   * into the final markup, which is then serialized by Gutenberg into `post_content`.
   *
   * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
   *
   * @return {Element} Element to render.
   */
  save() {
    return null;
  }
});
