'use strict';

import FormView from './form-view.es';
import FormListSelect from './form-list-select.es';
import FormInactive from './form-inactive.es';

const { BlockControls } = wp.blocks;
const { Component, createElement: el } = wp.element;
const { Spinner, withAPIData } = wp.components;

class FormEditor extends Component {

  constructor() {
		super( ...arguments );
	}

  render() {
    const { className, lists } = this.props;

    if ( lists.isLoading || 'undefined' === typeof lists.data ) {
      return el( 'div', { className: `${className} is-loading` }, el( Spinner ) );
    }

    if ( 0 >= lists.data.length ) {
      return el( FormInactive, {
        className: 'wp-chimp-inactive'
      });
    }

    return [
      el( BlockControls, {
        key: 'form-controls',
        className: `${className}__block-controls`
      }, el( FormListSelect, this.props ) ),
      el( FormView, this.props )
    ];
  }
}

export default withAPIData( () => {
  return {
    lists: '/wp-chimp/v1/lists?context=block'
  };
})( FormEditor );
