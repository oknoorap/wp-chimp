'use strict';

import { mount, el, setChildren } from 'redom';

import TableBody from './components/table-body.es';
import TablePagination from './components/table-pagination.es';

jQuery( function() {

  const tableBody = new TableBody();
  mount( document.querySelector( '#wp-chimp-table-lists' ), tableBody );

  const wpApiRoot = wpApiSettings.root.indexOf( '/wp-json/' );

  const settings = document.querySelector( '#wp-chimp-settings' );
  const settingsState = JSON.parse( settings.dataset.state );

  if ( 'undefined' === typeof wpApiSettings || -1 === wpApiRoot || false === settingsState.mailchimp.apiKey ) {
    tableBody.showEmptyState();
    return;
  }

  jQuery.ajax({
    url: `${wpApiSettings.root}wp-chimp/v1/lists`,
    type: 'GET',
    headers: {
      'X-WP-Nonce': wpApiSettings.nonce
    },
    beforeSend( xhr ) {
      tableBody.showPlaceholder();
    }
  })
  .done( ( lists, textStatus, request ) => {

    var totalPages = request.getResponseHeader( 'X-WP-Chimp-Lists-TotalPages' );
    var totalItems = request.getResponseHeader( 'X-WP-Chimp-Lists-Total' );

    if ( 2 >= totalPages ) {
      let tablePagination = new TablePagination();
      mount( document.querySelector( '#wp-chimp-lists' ), tablePagination.update( totalPages, totalItems ) );
    }

    tableBody.update( lists );
  })
  .fail( () => {
    tableBody.showEmptyState();
  });
});
