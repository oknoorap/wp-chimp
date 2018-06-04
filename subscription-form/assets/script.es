'use strict';

jQuery( ( $ ) => {

  if ( $( 'body' ).hasClass( 'wp-admin' ) ) {
    return;
  }

  $( '.wp-chimp-subscription-form__inputs' ).on( 'submit', ( event ) => {
    event.preventDefault();

    let formData = $( event.currentTarget ).serializeArray();
    console.log( formData );

  });

});
