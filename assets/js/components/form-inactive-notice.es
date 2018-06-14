'use strict';

import camelCaseKeys from 'camelcase-keys';

/**
 * Get the translateable strings for the Admin Settings page.
 *
 * @type {Object}
 */
const locale = camelCaseKeys( wpChimpL10n );
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
