'use strict';

import camelCaseKeys from 'camelcase-keys';
import snakeCaseKeys from 'snakecase-keys';

const wp = window.wp || {};
const { __ } = wp.i18n;
const { Component, createElement: el } = wp.element;
const { Dashicon } = wp.components;

class FormListSelect extends Component {

  constructor() {
    super( ...arguments );
	}

  render() {

    const { className, attributes, setAttributes, lists } = this.props;
    const { listId } = camelCaseKeys( attributes );

    let options = lists.data.map( object => {
      return el( 'option', {
        key: object.listId,
        value: object.listId
      }, object.name );
    });

    return el( 'div', {
      className: `${className}__select-list`
    }, [
      el( Dashicon, { icon: 'feedback' }),
      el( 'select', {
        value: listId,
        onChange: () => setAttributes( snakeCaseKeys({ listId: event.target.value }) )
      }, options )
    ]
   );
  }
}

export default FormListSelect;
