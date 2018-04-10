'use strict';

import { setChildren, el } from 'redom';

function getTableRows( data ) {

  var tableRows = [];
  for ( let i = 0; i < data.length; i++ ) {
    tableRows.push( el( 'tr', [
      el( 'td', [
        el( 'code', data[i].list_id )
      ]),
      el( 'td', data[i].name ),
      el( 'td', data[i].subscribers ),
      el( 'td', data[i].double_optin ),
      el( 'td', [
        el( 'code', `[wp-chimp list_id="${data[i].list_id}"]` )
      ])
    ]) );
  }

  return tableRows;
}

function getTableRowsPlaceholders() {

  var tableData = [];

  for ( let i = 0; 5 > i; i++ ) {
    tableData.push(
      el( 'td', [
        el( 'span', '', {
          'class': `wp-chimp-table-data-placeholder__bar index-${i}`
        })
      ])
    );
  }

  return el( 'tr', {
    'class': 'wp-chimp-table-data-placeholder'
  }, tableData );
}

jQuery( function( $ ) {
  if ( 'undefined' === typeof wpApiSettings || -1 === wpApiSettings.root.indexOf( '/wp-json/' ) ) {
    return;
  }

  const settings      = document.getElementById( 'wp-chimp-settings' );
  const listContainer = document.getElementById( 'wp-chimp-lists' );     // The table `tbody` element.
  const listNoItems   = document.getElementById( 'wp-chimp-no-lists' );  // Table empty state.
  const settingsState = JSON.parse( settings.dataset.state );
  const namespace     = 'wp-chimp/v1';

  if ( 'undefined' !== typeof settingsState.mailchimp.apiKey && true === settingsState.mailchimp.apiKey ) {

    $.ajax({
      'url': `${wpApiSettings.root}${namespace}/lists`,
      beforeSend() {
        setChildren( listContainer, getTableRowsPlaceholders() );
      }
    })
    .done( ( resp ) => {
      setChildren( listContainer, getTableRows( resp.lists ) );
    });
  }
});
