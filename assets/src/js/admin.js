import { getApiRootStatus } from './lib/utils'

import TableBody from './components/table/body'
import TableRequest from './components/table/request'
import TablePagination from './components/table/pagination'
import TableSync from './components/table/sync'

jQuery($ => {
  $(document).ready(() => {
    const { mailchimpApiStatus, listsInit } = wpChimpSettingState
    const tableBody = new TableBody()

    if (!getApiRootStatus() || !mailchimpApiStatus) {
      tableBody.mountEmptyState()
    } else {
      const tablePagination = new TablePagination()
      const tableRequest = new TableRequest(tableBody, tablePagination)

      new TableSync(tableRequest)

      if (!listsInit) {
        tableRequest.request({
          endpoint: '/sync/lists'
        })
      } else {
        tableRequest.request()
      }
    }
  })
})
