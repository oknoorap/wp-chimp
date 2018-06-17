'use strict';

import camelCaseKeys from 'camelcase-keys';

/**
 * Get the translateable strings for the Admin Settings page.
 *
 * @type {Object}
 */
const locale = camelCaseKeys( wpChimpL10n );
const { Component, createElement: el, RawHTML } = wp.element;

class FormInactive extends Component {

  constructor() {
    super( ...arguments );
	}

  render() {
    const { className } = this.props;

    return el( 'div', {
      key: 'form-inactive',
      className
    }, el( RawHTML, {
      key: 'form-inactive-content',
      className: `${className}__content`
    }, locale.inactiveNotice ) );
  }
}

export default FormInactive;
