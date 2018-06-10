'use strict';

import camelCaseKeys from 'camelcase-keys';
import snakeCaseKeys from 'snakecase-keys';

const wp = window.wp || {};
const { __ } = wp.i18n;
const { Component, createElement: el } = wp.element;
const { Dashicon, Spinner, withAPIData } = wp.components;

class ListSelect extends Component {

  render() {

    const { className, attributes, setAttributes, lists } = this.props;
    const { listId } = camelCaseKeys( attributes );

    if ( lists.isLoading || 'undefined' === typeof lists.data ) {
      return el( 'div', { className: `${className}__select-list` }, el( Spinner ) );
    }

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
        onChange: ( text ) => setAttributes( snakeCaseKeys({ listId: event.target.value }) )
      }, options )
    ]
   );
  }
}

export default withAPIData( () => {
  return { lists: '/wp-chimp/v1/lists?context=block' };
})( ListSelect );