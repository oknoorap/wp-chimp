( function( wp ) {

  const { __ } = wp.i18n;
  const { registerBlockType } = wp.blocks;
  const { createElement } = wp.element;
  const { SelectControl, Placeholder } = wp.components;

  /**
   * Every block starts by registering a new block type definition.
   *
   * @see https://wordpress.org/gutenberg/handbook/block-api/
   */
  registerBlockType( 'wp-chimp/form', {

    /**
     * This is the display title for your block, which can be translated with `i18n` functions.
     * The block inserter will show this name.
     */
    title: __( 'MailChimp Form' ),

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
    keywords: [ __( 'form' ), __( 'subscription' ), __( 'subscribe' ) ],

    // Block Attributes
    attributes: {
      mailChimpList: {
        type: 'string'
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
    edit({ className, attributes, setAttributes }) {
      const { mailChimpList } = attributes;

      /**
       * Set the selected MailChimp list
       *
       * @param  {string} value The MailChimp list ID.
       * @return {void}
       */
      function onSelectOption( value ) {
        setAttributes({
          mailChimpList: value
        });
      }

      return [
        createElement( Placeholder, {
          key: 'placeholder',
          icon: 'feedback',
          label: __( 'MailChimp Form' ),
          instructions: __( 'Select the MailChimp list that you would like to use on this form.' )
        }, [
          createElement( SelectControl, {
            className: `${className}__select-list`,
            value: mailChimpList,
            options: [ {
              value: '1',
              label: 'List 1'
            }, {
              value: '2',
              label: 'List 2'
            } ],
            onChange: onSelectOption
          })
        ])
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
}( window.wp || {}) );
