'use strict';

import { setChildren, el } from 'redom';

const locale = wpChimpLocaleAdmin;

function getTableRows( data ) {

  var tableRows = [];

  for ( let i = 0; i < data.length; i++ ) {
    tableRows.push( el( 'tr', [
      el( 'td', [
        el( 'code', data[i].listId )
      ]),
      el( 'td', data[i].name ),
      el( 'td', data[i].subscribers ),
      el( 'td', ( 0 === data[i].doubleOptin ? locale.no : locale.yes ) ),
      el( 'td', [
        el( 'code', `[wp-chimp list_id="${data[i].listId}"]` )
      ])
    ]) );
  }

  return tableRows;
}

function getTableRowNoItems( message ) {

  var tableData = [
    el( 'td', message ? message : locale.noLists, {
      'colspan': '5'
    })
  ];

  return el( 'tr', {
    'class': 'no-items',
    'id': 'wp-chimp-no-lists'
  }, tableData );
}

function getTableRowPlaceholders() {

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

jQuery( function() {

  const wpApiRoot       = wpApiSettings.root.indexOf( '/wp-json/' );
  const settings        = document.getElementById( 'wp-chimp-settings' );
  const settingsState   = JSON.parse( settings.dataset.state );
  const mailChimpApiKey = settingsState.mailchimp.apiKey;

  if ( 'undefined' === typeof wpApiSettings || -1 === wpApiRoot || false === mailChimpApiKey ) {
    setChildren( listContainer, getTableRowNoItems() );
    return;
  }

  const listContainer = document.getElementById( 'wp-chimp-lists' );
  const apiNamespace  = 'wp-chimp/v1';

  jQuery.ajax({
    url: `${wpApiSettings.root}${apiNamespace}/lists`,
    type: 'GET',
    headers: {
      'X-WP-Nonce': wpApiSettings.nonce
    },
    beforeSend( xhr ) {
      setChildren( listContainer, getTableRowPlaceholders() );
    }
  })
  .done( ( lists ) => {
    setChildren( listContainer, getTableRows( lists ) );
  })
  .fail( ( lists ) => {
    setChildren( listContainer, getTableRowNoItems( lists.responseJSON.message ) );
  });
});
