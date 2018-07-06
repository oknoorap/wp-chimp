'use strict';

import camelCaseKeys from 'camelcase-keys';
import snakeCaseKeys from 'snakecase-keys';

const wp = window.wp || {};
const { Component, createElement: el } = wp.element;
const { RichText } = wp.editor;

class FormView extends Component {

  constructor() {
    super( ...arguments );
	}

  render() {
    const { attributes, setAttributes, className } = this.props;
    const { headingText, subHeadingText, emailPlaceholderText, buttonText, footerText } = camelCaseKeys( attributes );

    return el( 'div', {
        className: `wp-chimp-block ${className}`
      }, [
      el( 'header', {
        className: `${className}__header`
      }, [
        el( RichText, {
          key: 'heading',
          format: 'string',
          tagName: 'h3',
          className: `${className}__heading is-editable`,
          value: headingText,
          formattingControls: [],
          onChange: ( value ) => setAttributes( snakeCaseKeys({ headingText: value }) )
        }),
        el( RichText, {
          key: 'sub-heading',
          format: 'string',
          tagName: 'p',
          className: `${className}__sub-heading is-editable`,
          value: subHeadingText,
          formattingControls: [ 'bold', 'italic', 'link' ],
          onChange: ( value ) => setAttributes( snakeCaseKeys({ subHeadingText: value }) )
        })
      ]),
      el( 'div', {
        className: 'wp-chimp-form'
      }, [
        el( 'div', {
          className: 'wp-chimp-form__fieldset'
        }, el( RichText, {
          key: 'email-field',
          format: 'string',
          tagName: 'div',
          className: 'wp-chimp-form__email-field is-editable',
          value: emailPlaceholderText,
          formattingControls: [],
          onChange: ( value ) => setAttributes( snakeCaseKeys({ emailPlaceholderText: value }) )
        }) ),
        el( 'div', {
          className: 'wp-chimp-form__button'
        }, el( RichText, {
          key: 'button',
          format: 'string',
          className: 'is-editable',
          tagName: 'div',
          value: buttonText,
          formattingControls: [],
          onChange: ( value ) => setAttributes( snakeCaseKeys({ buttonText: value }) )
        }) )
      ]),
      el( RichText, {
        key: 'footer',
        format: 'string',
        tagName: 'p',
        className: `${className}__footer is-editable`,
        value: footerText,
        formattingControls: [ 'bold', 'italic', 'link' ],
        onChange: ( value ) => setAttributes( snakeCaseKeys({ footerText: value }) )
      })
    ]);
  }
}

export default FormView;
