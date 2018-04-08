'use strict';

import { mount, el } from 'redom';

jQuery( function( $ ) {

  if ( 'undefined' === typeof wpApiSettings || -1 === wpApiSettings.root.indexOf( '/wp-json/' ) ) {
    return;
  }

  const $settings      = $( '#wp-chimp-settings' );
  const $settingsState = $settings.data( 'state' );
  const apiVersion     = 'wp-chimp/v1';

  $.ajax({
    'url': `${wpApiSettings.root}${apiVersion}/lists`
  })
  .done( ( resp ) => {
    console.log( resp );
  });

});
