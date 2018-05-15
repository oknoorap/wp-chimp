'use strict';

const wp = window.wp || {};
const { Component, createElement: el } = wp.element;
const { RichText } = wp.blocks;

class FormView extends Component {

  render() {

    const { className, attributes, setAttributes } = this.props;
    const { headingText, subHeadingText, inputEmailPlaceholder, buttonText } = attributes;

    return el( 'div', {
        className: `wp-chimp-block ${className}`
      }, [
      el( RichText, {
        key: 'form-heading',
        format: 'string',
        tagName: 'h3',
        className: `${className}__heading`,
        value: headingText,
        isSelected: false,
        onChange: ( text ) => setAttributes({ headingText: text })
      }),
      el( RichText, {
        key: 'form-sub-heading',
        format: 'string',
        tagName: 'p',
        className: `${className}__sub-heading`,
        value: subHeadingText,
        isSelected: false,
        onChange: ( text ) => setAttributes({ subHeadingText: text })
      }),
      el( 'div', { className: `${className}__inputs` }, [
        el( RichText, {
          key: 'form-input-email',
          format: 'string',
          tagName: 'div',
          className: `${className}__email-field`,
          value: inputEmailPlaceholder,
          isSelected: false,
          onChange: ( text ) => setAttributes({ inputEmailPlaceholder: text })
        }),
        el( RichText, {
          key: 'form-submit-button',
          format: 'string',
          tagName: 'div',
          className: `${className}__button`,
          value: buttonText,
          isSelected: false,
          onChange: ( text ) => setAttributes({ buttonText: text })
        })
      ])
    ]);
  }
}

export default FormView;
