'use strict';

import camelCaseKeys from 'camelcase-keys';

import ListSelect from './components/list-select.es';
import FormView from './components/form-view.es';

const wp = window.wp || {};

const locale = camelCaseKeys( wpChimpL10n );
const settingsState = camelCaseKeys( wpChimpSettingsState );

const { registerBlockType, BlockControls } = wp.blocks;
const { createElement: el, RawHTML } = wp.element;
const { Toolbar } = wp.components;
const { RichText } = wp.blocks;

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://wordpress.org/gutenberg/handbook/block-api/
 */
registerBlockType( 'wp-chimp/subscription-form', {

  /**
   * This is the display title for your block, which can be translated with `i18n` functions.
   * The block inserter will show this name.
   */
  title: locale.title,

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
   *
   * @type {String}
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
  keywords: [ 'form', 'subcribe', 'mailchimp' ],

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

    props.className = 'wp-chimp-subscription-form';

    const { className } = props;
    const { apiKey, apiKeyStatus, listsTotalItems } = settingsState;

    if ( ! apiKey || ! apiKeyStatus || 0 < listsTotalItems ) {

      return el( RawHTML, {
        key: 'form-controls-inactive',
        className: `${className}__block-controls ${className}__block-controls--inactive`
      }, locale.inactiveNotice );
    }

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
   * @return {null} Element to render.
   */
  save() {
    return null;
  }
});
