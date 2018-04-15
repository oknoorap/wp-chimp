import { setChildren, el } from 'redom';

class TablePagination {
  constructor() {
    this.el = el( 'div', {
      'id': 'wp-chimp-table-pagination',
      'class': 'tablenav-pages'
    });
  }
  update( totalPages, totalItems  ) {
    setChildren( this.el, [
      this.getTotalItems( totalItems ),
      this.getPaginationLinks( totalPages )
    ]);

    return this.el;
  }
  getPaginationLinks( totalPages ) {
    return el( 'span', {
      'class': 'pagination-links'
    }, [
      this.getPrevButton(),
      this.getPaginationInput( totalPages ),
      this.getNextButton()
    ]);
  }
  getTotalItems( totalItems ) {
    return el( 'span', {
      'class': 'displaying-num'
    }, `${totalItems} items` );
  }
  getPrevButton() {
    return el( 'span', {
      'class': 'prev-page'
    }, '‹' );
  }
  getNextButton() {
    return el( 'span', {
      'class': 'next-page'
    }, '›' );
  }
  getPaginationInput( totalPages ) {
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
        }, totalPages )
      ])
    ]);
  }
}

export default TablePagination;
