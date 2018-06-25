import { getApiRootStatus } from './components/utilities'
import FormEditor from './components/form-editor'
import FormInactive from './components/form-inactive'

const wp = window.wp || {}

const locale = wpChimpL10n
const { mailchimpApiStatus } = wpChimpSettingState
const { registerBlockType } = wp.blocks
const { createElement: el } = wp.element

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://wordpress.org/gutenberg/handbook/block-api/
 */
registerBlockType('wp-chimp/subscription-form', {

  /**
   * This is the display title for your block, which can be translated
   * with `i18n` functions. The block inserter will show this name.
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
   * The categories provided by core are `common`, `embed`, `formatting`,
   * `layout` and `widgets`.
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
   * The edit function describes the structure of your block in the context
   * of the editor.
   *
   * This represents what the editor will render when the block is used.
   *
   * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
   *
   * @param {Object} [props] Properties passed from the editor.
   * @return {Element}       Element to render.
   */
  edit (props) {
    if (!getApiRootStatus() || !mailchimpApiStatus) {
      return el(FormInactive, {
        className: 'wp-chimp-inactive'
      })
    }

    return el(FormEditor, {
      ...props,
      className: 'wp-chimp-subscription-form'
    })
  },

  /**
   * The save function defines the way in which the different attributes
   * should be combined into the final markup, which is then serialized
   * by Gutenberg into `post_content`.
   *
   * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
   *
   * @return {null} Element to render.
   */
  save () {
    return null
  }
})
