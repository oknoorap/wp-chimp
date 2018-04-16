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

    this.currentPage   = 1; // Default to page 1.
    this.totalPages = parseInt( totalPages, 10 );
    this.totalItems = parseInt( totalItems, 10 );

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
      'class': 'prev-page inactive',
      'data-page': 1
    }, '‹' );

    elem.addEventListener( 'click', this.paginationActions.bind( this ) );

    this.prevButton = elem;

    return elem;
  }
  getNextButton() {

    var elem = el( 'span', {
      'id': 'wp-chimp-table-pagination-next',
      'class': 'next-page',
      'data-page': 2
    }, '›' );

    elem.addEventListener( 'click', this.paginationActions.bind( this ) );

    this.nextButton = elem;

    return elem;
  }
  getPaginationInput() {

    var elemInput = el( 'input', {
      'id': 'current-page-selector',
      'class': 'current-page',
      'value': 1,
      'size': 3,
      'aria-describedby': 'table-paging',
      'type': 'text'
    });

    var elem = el( 'span', {
      'class': 'paging-input'
    }, [
      el( 'label', {
        'class': 'screen-reader-text',
        'for': 'current-page-selector'
      }, 'Current Page' ),
      elemInput,
      el( 'span', {
        'class': 'tablenav-paging-text'
      }, [
        'of',
        el( 'span', {
          'class': 'total-pages'
        }, this.totalPages )
      ])
    ]);

    elem.addEventListener( 'keypress', this.paginationActions.bind( this ) );

    this.paginationInput = elem;

    return elem;
  }
  paginationActions( event ) {

    var targetPage;
    var keyCode = event.which || event.keyCode;

    if ( 13 === keyCode ) {
      targetPage = parseInt( event.target.value, 10 );
    } else {
      targetPage = parseInt( event.target.dataset.page, 10 );
    }

    if ( ! Number.isInteger( targetPage ) ) {
      return;
    }

    this.tableRequest.request({ 'page': targetPage });
    this.currentPage = targetPage;

    this.toggleButtonState();
  }
  toggleButtonState() {

    if ( 1 === this.currentPage ) {
      this.prevButton.classList.add( 'inactive' );
    } else {
      this.prevButton.classList.remove( 'inactive' );
    }

    if ( this.currentPage >= this.totalPages ) {
      this.nextButton.classList.add( 'inactive' );
    } else {
      this.nextButton.classList.remove( 'inactive' );
    }
  }
}

export default TablePagination;
