'use strict';

const locale = wpChimpL10n;
const { Component, createElement: el, RawHTML } = wp.element;

class FormInactiveNotice extends Component {

  render() {
    return el( RawHTML, {
      key: 'form-controls-inactive',
      className: 'wp-chimp-notice'
    }, locale.inactiveNotice );
  }
}

export default FormInactiveNotice;
