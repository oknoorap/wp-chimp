'use strict';

import { getApiStatus, getMailChimpApiStatus } from './components/utilities.es';
import TableBody from './components/table-body.es';
import TableRequest from './components/table-request.es';

document.addEventListener( 'DOMContentLoaded', () => {

  const tableBody = new TableBody();

  const settings = document.querySelector( '#wp-chimp-settings' );
  const settingsState = JSON.parse( settings.dataset.state );

  if ( true !== getApiStatus() || true !== getMailChimpApiStatus( settingsState ) ) {
    tableBody.mountEmptyState();
  } else {
    const tableRequest = new TableRequest( tableBody );
    tableRequest.request();
  }
});
