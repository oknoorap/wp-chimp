'use strict';

import { getApiStatus } from './components/utilities.es';

import TableBody from './components/table-body.es';
import TableRequest from './components/table-request.es';
import TablePagination from './components/table-pagination.es';

document.addEventListener( 'DOMContentLoaded', () => {

  const tableBody = new TableBody();

  if ( false === getApiRootStatus() || ! apiKey || ! apiKeyStatus || 0 < listsTotalItems ) {
    tableBody.mountEmptyState();
  } else {

    const tablePagination = new TablePagination();
    const tableRequest = new TableRequest( tableBody, tablePagination );

    tableRequest.request();
  }
});
