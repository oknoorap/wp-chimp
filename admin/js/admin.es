'use strict';

import { setLocaleData } from '@wordpress/i18n';
import { getApiStatus, getMailChimpApiStatus } from './components/utilities.es';
import TableBody from './components/table-body.es';
import TableRequest from './components/table-request.es';
import TablePagination from './components/table-pagination.es';

document.addEventListener( 'DOMContentLoaded', () => {

  /**
   * Creates a new Jed instance with specified locale data configuration.
   *
   * {@link https://www.npmjs.com/package/@wordpress/i18n NPM Repository}.
   */
  setLocaleData( wpChimpLocaleConfigs, 'wp-chimp' );

  const tableBody = new TableBody();

  if ( true !== getApiStatus() || true !== getMailChimpApiStatus( wpChimpSettingsState ) ) {
    tableBody.mountEmptyState();
  } else {

    const tablePagination = new TablePagination();
    const tableRequest = new TableRequest( tableBody, tablePagination );

    tableRequest.request();
  }
});
