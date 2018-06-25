/**
 * Get the translateable strings for the Admin Settings page.
 *
 * @type {Object}
 */
const locale = wpChimpL10n
const { Component, createElement: el, RawHTML } = wp.element

class FormInactive extends Component {
  render () {
    const { className } = this.props

    return el('div', {
      key: 'form-inactive',
      className
    }, el(RawHTML, {
      key: 'form-inactive-content',
      className: `${className}__content`
    }, locale.inactiveNotice))
  }
}

export default FormInactive
