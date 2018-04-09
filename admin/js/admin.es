'use strict';

import { setChildren, el } from 'redom';

function getTheTableRows( data ) {

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

jQuery( function( $ ) {

  if ( 'undefined' === typeof wpApiSettings || -1 === wpApiSettings.root.indexOf( '/wp-json/' ) ) {
    return;
  }

  const settings      = document.getElementById( 'wp-chimp-settings' );
  const settingsState = JSON.parse( settings.dataset.state );

  const namespace = 'wp-chimp/v1';
  const tableBody = document.getElementById( 'wp-chimp-mailchimp-list-data' );

  if ( 'undefined' !== typeof settingsState.mailchimp.apiKey && true === settingsState.mailchimp.apiKey ) {
    $.ajax({
      'url': `${wpApiSettings.root}${namespace}/lists`
    })
    .done( ( resp ) => {
      var rows = getTheTableRows( resp );
      setChildren( tableBody, rows );
    });
  }
});
