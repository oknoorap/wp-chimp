'use strict';

import FormView from './form-view';
import FormListSelect from './form-list-select';
import FormInactive from './form-inactive';

const { listsTotalItems } = wpChimpSettingState;

const { BlockControls } = wp.editor;
const { Component, createElement: el } = wp.element;
const { Toolbar, Spinner, withAPIData } = wp.components;

class FormEditor extends Component {

  constructor() {
		super( ...arguments );
	}

  render() {
    const { className, lists } = this.props;
    const { isLoading, data } = lists;

    if ( isLoading || 'undefined' === typeof data ) {
      return el( 'div', { className: `${className} is-loading` }, el( Spinner ) );
    }

    if ( 0 >= data.length ) {
      return el( FormInactive, {
        className: 'wp-chimp-inactive'
      });
    }

    return [
      el( BlockControls, {
        key: 'form-controls',
        className: `${className}__block-controls`
      }, el( Toolbar, {
        key: 'form-toolbar'
      }, el( FormListSelect, this.props ) ) ),
      el( FormView, this.props )
    ];
  }
}

export default withAPIData( () => {
  return {
    lists: `/wp-chimp/v1/lists?per_page=${listsTotalItems}`
  };
})( FormEditor );
