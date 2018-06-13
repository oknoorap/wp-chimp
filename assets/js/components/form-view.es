'use strict';

import camelCaseKeys from 'camelcase-keys';
import snakeCaseKeys from 'snakecase-keys';

const wp = window.wp || {};
const { Component, createElement: el } = wp.element;
const { RichText } = wp.editor;
const { withState } = wp.components;

class FormView extends Component {

  onSetActiveEditable( newEditable ) {
    const { setState } = this.props;
    setState({ editable: newEditable });
  }

  render() {

    const { attributes, setAttributes, isSelected, className, editable } = this.props;
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
        formattingControls: [],
        onChange: ( text ) => setAttributes( snakeCaseKeys({ headingText: text }) )
      }),
      el( RichText, {
        key: 'sub-heading',
        format: 'string',
        tagName: 'p',
        className: `${className}__sub-heading ${className}--editable`,
        value: subHeadingText,
        formattingControls: [ 'bold', 'italic', 'link' ],
        onChange: ( text ) => setAttributes( snakeCaseKeys({ subHeadingText: text }) )
      }),
      el( 'div', { className: `${className}__inputs` }, [
        el( RichText, {
          key: 'input-email',
          format: 'string',
          tagName: 'div',
          className: `${className}__email-field ${className}--editable`,
          value: emailPlaceholderText,
          formattingControls: [],
          onChange: ( text ) => setAttributes( snakeCaseKeys({ emailPlaceholderText: text }) )
        }),
        el( RichText, {
          key: 'submit-button',
          format: 'string',
          tagName: 'div',
          className: `${className}__button ${className}--editable`,
          value: buttonText,
          formattingControls: [],
          onChange: ( text ) => setAttributes( snakeCaseKeys({ buttonText: text }) )
        })
      ]),
      el( RichText, {
        key: 'footer',
        format: 'string',
        tagName: 'p',
        className: `${className}__footer ${className}--editable`,
        value: footerText,
        formattingControls: [ 'bold', 'italic', 'link' ],
        onChange: ( text ) => setAttributes( snakeCaseKeys({ footerText: text }) )
      })
    ]);
  }
}

export default withState({
	editable: null
})( FormView );
