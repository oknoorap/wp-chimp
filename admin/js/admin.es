'use strict';

import { getApiRootStatus, getMailChimpApiStatus } from './components/utilities.es';

import TableBody from './components/table-body.es';
import TableRequest from './components/table-request.es';
import TablePagination from './components/table-pagination.es';

document.addEventListener( 'DOMContentLoaded', () => {

  const tableBody = new TableBody();

  if ( false === getApiRootStatus() || false === getMailChimpApiStatus() ) {
    tableBody.mountEmptyState();
  } else {

    const tablePagination = new TablePagination();
    const tableRequest = new TableRequest( tableBody, tablePagination );

    tableRequest.request();
  }
});
