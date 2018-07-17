const { Component } = wp.element
const { RichText } = wp.editor

class FormView extends Component {
  render () {
    const { attributes, setAttributes, className } = this.props
    const {
      heading_text: headingText,
      sub_heading_text: subHeadingText,
      email_placeholder_text: emailPlaceholderText,
      button_text: buttonText,
      footer_text: footerText
    } = attributes

    return (
      <div className={`wp-chimp-block ${className}`}>
        <RichText
          key="heading"
          format="string"
          tagName="h3"
          className={`${className}__heading is-editable`}
          value={headingText}
          formattingControls={[]}
          onChange={value => setAttributes({ heading_text: value }) } />

        <RichText
          key="sub-heading"
          format="string"
          tagName="p"
          className={`${className}__sub-heading is-editable`}
          value={subHeadingText}
          formattingControls={[ 'bold', 'italic', 'link' ]}
          onChange={value => setAttributes({ sub_heading_text: value })} />

        <div className="wp-chimp-form">
          <fieldset className="wp-chimp-form__fieldset">
            <RichText
              key="email-field"
              format="string"
              tagName="div"
              className="wp-chimp-form__email-field is-editable"
              value={emailPlaceholderText}
              formattingControls={[]}
              onChange={value => setAttributes({ email_placeholder_text: value })} />

            <RichText
              key="button"
              format="string"
              tagName="div"
              className="wp-chimp-form__button is-editable"
              value={buttonText}
              formattingControls={[]}
              onChange={value => setAttributes({ button_text: value })} />
          </fieldset>

          <RichText
            key="footer"
            format="string"
            tagName="p"
            className={`${className}__footer is-editable`}
            value={footerText}
            formattingControls={[ 'bold', 'italic', 'link' ]}
            onChange={value => setAttributes({ footer_text: value })} />
        </div>
      </div>
    )
  }
}

export default FormView
