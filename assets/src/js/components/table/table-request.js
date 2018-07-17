/**
 * The Class to make a request to wp-chimp API enpoint
 *
 * @since 0.1.0
 */
class TableRequest {
  /**
   * The Class constructor.
   *
   * @since 0.1.0
   *
   * @param {TableBody} tableBody
   * @param {TablePagination} tablePagination
   */
  constructor (tableBody, tablePagination) {
    const { nonce: wpChimpNonce, wpRestNonce } = wpChimpSettingState

    this.currentPage = 1
    this.tableBody = tableBody
    this.tablePagination = tablePagination
    this.configs = {
      type: 'GET',
      headers: {
        'X-WP-Nonce': wpRestNonce,
        'X-WP-Chimp-Nonce': wpChimpNonce
      },
      data: {
        'per_page': 10
      },
      beforeSend: () => this.tableBody.mountPlaceholder()
    }

    this.bindEvents()
  }

  /**
   * Bind the pagination elements to an Event.
   *
   * @since 0.1.0
   */
  bindEvents () {
    this.tablePagination.prevButton.addEventListener('click', this.actions.bind(this))
    this.tablePagination.nextButton.addEventListener('click', this.actions.bind(this))
    this.tablePagination.inputField.addEventListener('keypress', this.actions.bind(this))
  }

  /**
   * The addEventListener callback when the button is clicked, or when the input
   * field value is changed.
   *
   * @since 0.1.0
   *
   * @param {Event} event
   */
  actions (event) {
    var targetPage
    var keyCode = event.which || event.keyCode

    if (keyCode === 13) {
      targetPage = parseInt(event.target.value, 10)
    } else {
      targetPage = parseInt(event.target.dataset.page, 10)
    }

    if (Number.isInteger(targetPage) && targetPage >= 1 && this.currentPage !== targetPage) {
      this.request({
        'page': targetPage
      })
    }
  }

  /**
   * Make a request to the WP-API to pull the lists.
   *
   * @since 0.1.0
   *
   * @param {Object} args The extra arguments to pass in to the Ajax config.
   */
  request (args = {}) {
    const { restApiUrl } = wpChimpSettingState

    this.configs.url = `${restApiUrl}${args.hasOwnProperty('url') ? args.endpoint : '/lists'}`

    if (args.hasOwnProperty('page')) {
      let page = parseInt(args.page, 10)
      this.configs.data.page = page < 1 ? 1 : page
    }

    /**
     * TODO: Phasing out jQuery.
     *
     * Make the code less reliant on jQuery.
     */
    jQuery
      .ajax(this.configs)
      .done(this.onSuccess.bind(this))
      .fail(this.onFailed.bind(this))
  }

  /**
   * The function to be called if the request succeeds.
   *
   * @since 0.1.0
   *
   * @param {Object} lists
   * @param {string} textStatus
   * @param {jqXHR} request The jQuery XMLHttpRequest (jqXHR) object returned by $.ajax()
   */
  onSuccess (lists, textStatus, request) {
    if (lists.length === 0) {
      this.tableBody.mountEmptyState()
    } else {
      let totalPages = request.getResponseHeader('X-WP-Chimp-Lists-TotalPages')
      let totalItems = request.getResponseHeader('X-WP-Chimp-Lists-Total')
      let currentPage = request.getResponseHeader('X-WP-Chimp-Lists-Page')

      totalPages = parseInt(totalPages, 10)
      totalItems = parseInt(totalItems, 10)
      currentPage = parseInt(currentPage, 10)

      this.tableBody.update(lists)
      this.tablePagination.update(currentPage, totalPages, totalItems)
      this.currentPage = currentPage
    }
  }

  /**
   * The function to be called if the request fails.
   *
   * @since 0.1.0
   */
  onFailed () {
    this.tableBody.mountEmptyState()
  }
}

export default TableRequest
