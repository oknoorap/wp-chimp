import { mount } from 'redom';
import TablePagination from './table-pagination.es';

class TableRequest {
  constructor( tableBody ) {

    this.tableBody = tableBody;
    this.configs = {
      url: `${wpApiSettings.root}wp-chimp/v1/lists`,
      type: 'GET',
      headers: {
        'X-WP-Nonce': wpApiSettings.nonce
      },
      beforeSend: this.tableBody.mountPlaceholder(),
      data: {}
    };
  }

  request( args = {}) {

    if ( args.hasOwnProperty( 'page' ) && 2 >= args.page ) {
      this.configs.data.page = parseInt( args.page, 10 );
    }

    jQuery
      .ajax( this.configs )
      .done( this.onSuccess.bind( this ) )
      .fail( this.onFailed.bind( this ) );
  }

  onSuccess( lists, textStatus, request ) {
    if ( 0 === lists.length ) {
      this.tableBody.mountEmptyState();
    } else {
      this.tableBody.update( lists );

      if ( null === document.querySelector( '#wp-chimp-table-pagination' ) ) {
        let totalPages = request.getResponseHeader( 'X-WP-Chimp-Lists-TotalPages' );
        let totalItems = request.getResponseHeader( 'X-WP-Chimp-Lists-Total' );
        let currPage   = request.getResponseHeader( 'X-WP-Chimp-Lists-Page' );

        this.loadPagination( totalPages, totalItems, currPage );
      }
    }
  }

  onFailed() {
    this.tableBody.mountEmptyState();
  }

  loadPagination( totalPages, totalItems, currPage ) {
    if ( 2 >= totalPages ) {
      let tablePagination = new TablePagination( this.tableBody, this );
      mount( document.querySelector( '#wp-chimp-lists' ), tablePagination.update( totalPages, totalItems ) );
    }
  }
}

export default TableRequest;
