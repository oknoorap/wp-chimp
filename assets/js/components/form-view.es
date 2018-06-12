'use strict';

import camelCaseKeys from 'camelcase-keys';
import snakeCaseKeys from 'snakecase-keys';

const wp = window.wp || {};
const { Component, createElement: el } = wp.element;
const { RichText } = wp.editor;

class FormView extends Component {

  render() {

    const { className, attributes, setAttributes } = this.props;
    const { headingText, subHeadingText, emailPlaceholderText, buttonText, footerText } = camelCaseKeys( attributes );

    return el( 'div', {
        className: `wp-chimp-block ${className}`
      }, [
      el( RichText, {
        key: 'heading',
        format: 'string',
        tagName: 'h3',
        className: `${className}__heading ${className}--editable`,
        value: headingText,
        isSelected: false,
        onChange: ( text ) => setAttributes( snakeCaseKeys({ headingText: text }) )
      }),
      el( RichText, {
        key: 'sub-heading',
        format: 'string',
        tagName: 'p',
        className: `${className}__sub-heading ${className}--editable`,
        value: subHeadingText,
        isSelected: false,
        onChange: ( text ) => setAttributes( snakeCaseKeys({ subHeadingText: text }) )
      }),
      el( 'div', { className: `${className}__inputs` }, [
        el( RichText, {
          key: 'input-email',
          format: 'string',
          tagName: 'div',
          className: `${className}__email-field ${className}--editable`,
          value: emailPlaceholderText,
          isSelected: false,
          onChange: ( text ) => setAttributes( snakeCaseKeys({ emailPlaceholderText: text }) )
        }),
        el( RichText, {
          key: 'submit-button',
          format: 'string',
          tagName: 'div',
          className: `${className}__button ${className}--editable`,
          value: buttonText,
          isSelected: false,
          onChange: ( text ) => setAttributes( snakeCaseKeys({ buttonText: text }) )
        })
      ]),
      el( RichText, {
        key: 'footer',
        format: 'string',
        tagName: 'p',
        className: `${className}__footer ${className}--editable`,
        value: footerText,
        isSelected: false,
        onChange: ( text ) => setAttributes( snakeCaseKeys({ footerText: text }) )
      })
    ]);
  }
}

export default FormView;
