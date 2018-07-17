'use strict';

/**
 * The Class to sync the lists.
 *
 * @since 0.1.0
 */
class TableSync {

  /**
   * Set up the sync button and the TableRequest instance.
   *
   * @since 0.1.0
   *
   * @param {TableRequest} tableRequest
   */
  constructor( tableRequest ) {

    this.tableRequest = tableRequest;

    this.syncButton = document.querySelector( '#wp-chimp-sync-lists-button' );
    this.syncButton.addEventListener( 'click', this.onClick.bind( this ) );
  }

  /**
   * Sync the lists when the button is clicked.
   *
   * @since 0.1.0
   */
  onClick() {
    this.tableRequest.request({
      'endpoint': '/sync/lists'
    });
  }
}

export default TableSync;
