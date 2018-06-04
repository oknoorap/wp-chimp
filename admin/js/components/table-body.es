'use strict';

import camelCaseKeys from 'camelcase-keys';
import { mount, el, list, setChildren } from 'redom';

/**
 * Get the translateable strings for the Admin Settings page.
 *
 * @type {Object}
 */
const locale = camelCaseKeys( wpChimpL10n );

/**
 * The Class to render the table row (`tr`) and data (`td`) elements.
 *
 * @since 0.1.0
 */
class TableRow {

  constructor() {
    this.el = el( 'tr' );
  }

  /**
   * The function to render the MailChimp list item into
   * a table row and data elements.
   *
   * @since 0.1.0
   *
   * @param {Object} list
   */
  update( list ) {
    setChildren( this.el, this.getTableData( list ) );
  }

  /**
   * The function to render the MailChimp list item to
   * the table data elements.
   *
   * @since 0.1.0
   *
   * @param {Object} list
   */
  getTableData( list ) {

    return [
      el( 'td', [
        el( 'code', list.listId )
      ]),
      el( 'td', list.name ),
      el( 'td', list.subscribers ),
      el( 'td', ( 0 === list.doubleOptin ? locale.no : locale.yes ) ),
      el( 'td', [
        el( 'code', `[wp-chimp list_id="${list.listId}"]` )
      ])
    ];
  }
}

/**
 * The Class to render the table body (`tbody`) element.
 *
 * @since 0.1.0
 */
class TableBody {

  constructor() {

    this.el   = el( 'tbody', { 'id': 'wp-chimp-table-lists-body' });
    this.list = list( this.el, TableRow );

    mount( document.querySelector( '#wp-chimp-table-lists' ), this );
  }

  /**
   * Function to update the list to the table.
   *
   * @since 0.1.0
   *
   * @param {Object} data
   */
  update( data ) {
    this.list.update( data );
  }

  /**
   * Render the table row (`tr`) and data (`td`) when the list is empty.
   *
   * @since 0.1.0
   */
  mountEmptyState() {

    setChildren( this.el, el( 'tr', [
      el( 'td', locale.noLists, {
        'colspan': '5'
      })
    ]) );
  }

  /**
   * Render the table row (`tr`) and data (`td`) is being fetched.
   *
   * @since 0.1.0
   */
  mountPlaceholder() {

    var placeholder = [];
    for ( let i = 0; 5 > i; i++ ) {
      placeholder.push( el( 'td', [
        el( 'span', '', {
          'class': `wp-chimp-table-data-placeholder__bar index-${i}`
        })
      ]) );
    }
    setChildren( this.el, el( 'tr', placeholder ) );
  }
}

export default TableBody;
