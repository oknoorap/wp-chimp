import { setChildren, el } from 'redom';

class TablePagination {
  constructor( tableBody, tableRequest ) {
    this.tableBody = tableBody;
    this.tableRequest = tableRequest;
    this.el = el( 'div', {
      'id': 'wp-chimp-table-pagination',
      'class': 'tablenav-pages'
    });
  }
  update( totalPages, totalItems ) {

    this.totalPages = totalPages;
    this.totalItems = totalItems;

    setChildren( this.el, [
      this.getTotalItems(),
      this.getPaginationLinks()
    ]);

    return this.el;
  }
  getPaginationLinks() {
    return el( 'span', {
      'class': 'pagination-links'
    }, [
      this.getPrevButton(),
      this.getPaginationInput(),
      this.getNextButton()
    ]);
  }
  getTotalItems() {
    return el( 'span', {
      'class': 'displaying-num'
    }, `${this.totalItems} items` );
  }
  getPrevButton() {

    var elem = el( 'span', {
      'id': 'wp-chimp-table-pagination-prev',
      'class': 'prev-page',
      'data-page': 1
    }, '‹' );

    elem.addEventListener( 'click', this.paginationActions.bind( this ) );

    this.elPrevButton = elem;
    return elem;
  }
  getNextButton() {

    var elem = el( 'span', {
      'id': 'wp-chimp-table-pagination-next',
      'class': 'next-page',
      'data-page': 2
    }, '›' );

    elem.addEventListener( 'click', this.paginationActions.bind( this ) );

    this.elNextButton = elem;
    return elem;
  }
  getPaginationInput() {

    return el( 'span', {
      'class': 'paging-input'
    }, [
      el( 'label', {
        'class': 'screen-reader-text',
        'for': 'current-page-selector'
      }, 'Current Page' ),
      el( 'input', {
        'id': 'current-page-selector',
        'class': 'current-page',
        'value': 1,
        'size': 3,
        'aria-describedby': 'table-paging',
        'type': 'text'
      }),
      el( 'span', {
        'class': 'tablenav-paging-text'
      }, [
        'of',
        el( 'span', {
          'class': 'total-pages'
        }, this.totalPages )
      ])
    ]);
  }
  paginationActions( event ) {
    this.tableRequest.request({
      'page': event.target.dataset.page
    });
  }
}

export default TablePagination;
