class TableRequest {

  constructor( tableBody, tablePagination ) {

    this.tableBody = tableBody;
    this.tablePagination = tablePagination;
    this.configs = {
      url: `${wpApiSettings.root}wp-chimp/v1/lists`,
      type: 'GET',
      headers: {
        'X-WP-Nonce': wpApiSettings.nonce
      },
      beforeSend: this.tableBody.mountPlaceholder(),
      data: {}
    };

    this.bindEvents();
  }

  bindEvents() {

    this.tablePagination.prevButton.addEventListener( 'click', this.actions.bind( this ) );
    this.tablePagination.nextButton.addEventListener( 'click', this.actions.bind( this ) );
    this.tablePagination.inputField.addEventListener( 'keypress', this.actions.bind( this ) );
  }

  /**
   * The addEventListener callback when the button is clicked, or when the input
   * field value is changed.
   *
   * @since 0.1.0
   *
   * @param event
   */
  actions( event ) {

    var targetPage;
    var keyCode = event.which || event.keyCode;

    if ( 13 === keyCode ) {
      targetPage = parseInt( event.target.value, 10 );
    } else {
      targetPage = parseInt( event.target.dataset.page, 10 );
    }

    if ( ! Number.isInteger( targetPage ) ) { // The targetPage could be NaN. So, check it.
      return;
    }

    this.request({
      'page': targetPage
    });
  }

  request( args = {}) {

    if ( args.hasOwnProperty( 'page' ) ) {
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
      this.tablePagination.update( request );
    }
  }

  onFailed( lists, textStatus ) {
    this.tableBody.mountEmptyState();
  }
}

export default TableRequest;
