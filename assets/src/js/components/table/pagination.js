import { el, mount, setChildren } from 'redom'

/**
 * The Class to render the pagination for the table.
 *
 * @since 0.1.0
 */
class TablePagination {
  constructor () {
    this.el = el('div', {
      id: 'wp-chimp-table-pagination',
      className: 'tablenav-pages'
    })

    this.nextButton = this.getNextButton()
    this.prevButton = this.getPrevButton()
    this.inputField = this.getInputField()
  }

  /**
   * Render and append the pagination element.
   *
   * @since 0.1.0
   *
   * @param {string} totalPages
   * @param {string} totalItems
   * @return {Element}
   */
  render (totalPages, totalItems) {
    // Default to page 1.
    this.currentPage = 1

    // Turn it into integer.
    this.totalPages = parseInt(totalPages, 10)

    // Turn it into integer.
    this.totalItems = parseInt(totalItems, 10)

    setChildren(this.el, [
      this.renderTotalItem(),
      this.renderPagination()
    ])

    return this.el
  }

  /**
   * Render the elements that make up the pagination.
   *
   * These include the "prev button", the "next button", and the
   * input field allowing to jump up to the preferred page
   * number.
   *
   * @since 0.1.0
   *
   * @return {Element}
   */
  renderPagination () {
    return el('span', { 'class': 'pagination-links' }, [
      this.prevButton,
      this.getPaginationInput(),
      this.nextButton
    ])
  }

  /**
   * Render the element showing the total items in the list.
   *
   * @since 0.1.0
   *
   * @return {Element}
   */
  renderTotalItem () {
    return el('span', {
      'class': 'displaying-num'
    }, `${this.totalItems} items`)
  }

  /**
   * Get the "Previous" button element.
   *
   * @since 0.1.0
   *
   * @return {Element}
   */
  getPrevButton () {
    return el('span', {
      'id': 'wp-chimp-table-pagination-prev',
      'class': 'prev-page inactive'
    }, '‹')
  }

  /**
   * Get the "Next" button element.
   *
   * @since 0.1.0
   *
   * @returns {Element}
   */
  getNextButton () {
    return el('span', {
      'id': 'wp-chimp-table-pagination-next',
      'class': 'next-page'
    }, '›')
  }

  /**
   * Get the pagination "Input" field.
   *
   * This input element will allow the user to jump up to the preferred page
   * quickly.
   *
   * @since 0.1.0
   *
   * @returns {Element}
   */
  getPaginationInput () {
    return el('span', {
      'class': 'paging-input'
    }, [
      el('label', {
        'class': 'screen-reader-text',
        'for': 'current-page-selector'
      }, 'Current Page'),
      this.inputField,
      el('span', {
        'class': 'tablenav-paging-text'
      }, [
        'of',
        el('span', {
          'class': 'total-pages'
        }, this.totalPages)
      ])
    ])
  }

  getInputField () {
    return el('input', {
      'id': 'current-page-selector',
      'class': 'current-page',
      'value': 1,
      'size': 3,
      'aria-describedby': 'table-paging',
      'type': 'text'
    })
  }

  /**
   * Function to update the pagination UI.
   *
   * @since 0.1.0
   *
   * @param {integer} currentPage
   * @param {integer} totalPages
   * @param {integer} totalItems
   */
  update (currentPage, totalPages, totalItems) {
    if (totalPages <= 1) {
      return
    }

    if (!document.querySelector('#wp-chimp-table-pagination')) {
      mount(document.querySelector('#wp-chimp-lists'), this.render(totalPages, totalItems))
    }

    currentPage = parseInt(currentPage, 10)

    this.toggleActive(this.prevButton, currentPage === 1, currentPage)
    this.toggleActive(this.nextButton, currentPage >= this.totalPages, currentPage)

    this.inputField.value = currentPage < 1 ? 1 : currentPage
  }

  /**
   * Function to toggle the active state of the pagination button.
   *
   * @since 0.1.0
   *
   * @param {Element} elem
   * @param {boolean} state
   * @param {integer} currentPage
   */
  toggleActive (elem, state, currentPage) {
    var elemId = elem.getAttribute('id')
    var increment = elemId === 'wp-chimp-table-pagination-next' ? currentPage + 1 : currentPage - 1

    if (state) {
      elem.classList.add('inactive')
    } else {
      elem.classList.remove('inactive')
    }

    elem.dataset.page = state ? 0 : increment
  }
}

export default TablePagination
