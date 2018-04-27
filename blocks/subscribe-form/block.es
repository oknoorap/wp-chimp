( function( wp ) {

  const { setLocaleData, __ } = wp.i18n;
  const { registerBlockType } = wp.blocks;
  const { createElement: el } = wp.element;

  /**
   * Creates a new Jed instance with specified locale data configuration for the plugin.
   *
   * @see https://www.npmjs.com/package/@wordpress/i18n
   */
  setLocaleData( wpChimpLocaleConfigs, 'wp-chimp' );

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

      // Removes support for an HTML mode.
      html: false
    },

    /**
     * Make it easier to discover a block with keyword aliases
     *
     * @type {Array}
     */
    keywords: [ __( 'form', 'wp-chimp' ), __( 'subscription', 'wp-chimp' ), __( 'subscribe', 'wp-chimp' ) ],

    /**
     * The edit function describes the structure of your block in the context of the editor.
     * This represents what the editor will render when the block is used.
     *
     * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
     *
     * @param {Object} [props] Properties passed from the editor.
     * @return {Element}       Element to render.
     */
    edit({ className, attributes, setAttributes }) {

      return el( 'div', {
        key: 'subscribe-form-container',
        className: className
      }, [
        el( 'h3', {
          key: 'subscribe-form-heading',
          className: `${className}__heading`,
          contentEditable: true
        }, __( 'Get notified of our next update right to your inbox', 'wp-chimp' ) ),

        el( 'p', {
          key: 'subscribe-form-sub-heading',
          className: `${className}__sub-heading`,
          contentEditable: true
        }, __( 'Subscribe to our newsletter', 'wp-chimp' ) ),

        el( 'div', {
          key: 'subscribe-form-inputs',
          className: `${className}__inputs`
        }, [
          el( 'div', {
            key: 'subscribe-form-field',
            className: `${className}__field`,
            contentEditable: true
          }, __( 'Enter your email address', 'wp-chimp' ) ),
          el( 'div', {
            key: 'subscribe-form-button',
            className: `${className}__button`,
            contentEditable: true
          }, __( 'Subscribe', 'wp-chimp' ) )

        ])
      ]);
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
}( window.wp || {}) );
