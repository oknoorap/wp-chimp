'use strict';

import { getApiRootStatus } from './components/utilities.es';

import TableBody from './components/table-body.es';
import TableRequest from './components/table-request.es';
import TablePagination from './components/table-pagination.es';
import TableSync from './components/table-sync.es';

jQuery( () => {

  const { mailchimpApiStatus, listsInit } = wpChimpSettingState;
  const tableBody = new TableBody();

  if ( false === getApiRootStatus() || false === mailchimpApiStatus ) {
    tableBody.mountEmptyState();
  } else {

    const tablePagination = new TablePagination();
    const tableRequest = new TableRequest( tableBody, tablePagination );
    const tableSync = new TableSync( tableRequest );

    if ( false === listsInit ) {
      tableRequest.request({
        'url': 'wp-chimp/v1/sync/lists'
      });
    } else {
      tableRequest.request();
    }
  }
});
