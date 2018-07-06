'use strict';

import { getApiRootStatus } from './components/utilities';

import TableBody from './components/table-body';
import TableRequest from './components/table-request';
import TablePagination from './components/table-pagination';
import TableSync from './components/table-sync';

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
        'endpoint': '/sync/lists'
      });
    } else {
      tableRequest.request();
    }
  }
});
