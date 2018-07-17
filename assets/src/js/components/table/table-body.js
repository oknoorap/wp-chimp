/**
 * Get the translateable strings for the Admin Settings page.
 *
 * @type {Object}
 */
const locale = wpChimpL10n

/**
 * The Class to render the table row (`tr`) and data (`td`) elements.
 *
 * @since 0.1.0
 */
class TableRow {
  constructor () {
    this.el = el('tr')
  }

  /**
   * The function to render the MailChimp list item into
   * a table row and data elements.
   *
   * @since 0.1.0
   *
   * @param {Object} data
   */
  update (data) {
    setChildren(this.el, this.getTableData(data))
  }

  /**
   * The function to render the MailChimp list item to
   * the table data elements.
   *
   * @since 0.1.0
   *
   * @param {Object} data
   */
  getTableData (data) {
    const list = camelCaseKeys(data)
    const dashiconsNo = el('span', { className: 'dashicons dashicons-no-alt' })
    const dashiconsYes = el('span', { className: 'dashicons dashicons-yes' })

    return [
      el('td', {
        className: 'wp-chimp-table__td-list-id'
      }, [
        el('code', list.listId)
      ]),
      el('td', {
        className: 'wp-chimp-table__td-name'
      }, list.name),
      el('td', {
        className: 'wp-chimp-table__td-subscribers'
      }, list.subscribers),
      el('td', {
        className: 'wp-chimp-table__td-double-optin'
      }, list.doubleOptin === 0 || !list.doubleOptin ? dashiconsNo : dashiconsYes),
      el('td', {
        className: 'wp-chimp-table__td-shortcode'
      }, el('code', `[wp-chimp list_id="${list.listId}"]`))
    ]
  }
}

/**
 * The Class to render the table body (`tbody`) element.
 *
 * @since 0.1.0
 */
class TableBody {
  constructor () {
    this.el = el('tbody', { id: 'wp-chimp-table-lists-body' })
    this.list = list(this.el, TableRow)

    mount(document.querySelector('#wp-chimp-table-lists'), this)
  }

  /**
   * Function to update the list to the table.
   *
   * @since 0.1.0
   *
   * @param {Object} data
   */
  update (data) {
    this.list.update(data)
  }

  /**
   * Render the table row (`tr`) and data (`td`) when the list is empty.
   *
   * @since 0.1.0
   */
  mountEmptyState () {
    setChildren(this.el, el('tr', [
      el('td', locale.noLists, {
        'colspan': '5'
      })
    ]))
  }

  /**
   * Render the table row (`tr`) and data (`td`) is being fetched.
   *
   * @since 0.1.0
   */
  mountPlaceholder () {
    var placeholder = []
    for (let i = 0; i < 5; i++) {
      placeholder.push(el('td', [
        el('span', '', {
          className: `wp-chimp-table-data-placeholder__bar index-${i}`
        })
      ]))
    }
    setChildren(this.el, el('tr', placeholder))
  }
}

export default TableBody
