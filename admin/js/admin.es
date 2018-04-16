'use strict';

import TableBody from './components/table-body.es';
import TableRequest from './components/table-request.es';

document.addEventListener( 'DOMContentLoaded', () => {

  const tableBody = new TableBody();
  if ( 'undefined' === typeof wpApiSettings || 'undefined' === typeof wpApiSettings.root ) {
    tableBody.mountEmptyState();
  }

  const settings = document.querySelector( '#wp-chimp-settings' );
  const settingsState = JSON.parse( settings.dataset.state );

  if ( -1 === wpApiSettings.root.indexOf( '/wp-json/' ) || false === settingsState.mailchimp.apiKey ) {
    tableBody.mountEmptyState();
    return;
  }

  const tableRequest = new TableRequest( tableBody );
  tableRequest.request();
});
