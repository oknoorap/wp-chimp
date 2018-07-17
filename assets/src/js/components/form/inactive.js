/**
 * Get the translateable strings for the Admin Settings page.
 *
 * @type {Object}
 */
const locale = wpChimpL10n
const { Component, RawHTML } = wp.element

class FormInactive extends Component {
  render () {
    const { className } = this.props

    return (
      <div key="form-inactive" className={className}>
        <RawHTML key="form-inactive-content" className={`${className}__content`}>
          {locale.inactiveNotice}
        </RawHTML>
      </div>
    )
  }
}

export default FormInactive
