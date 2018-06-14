'use strict';

jQuery( ( $ ) => {
  if ( $( 'body' ).hasClass( 'wp-admin' ) ) {
    return;
  }

  $( 'body' ).on( 'submit', '.wp-chimp-subscription-form__form', ( event ) => {
    event.preventDefault();

    let form = $( event.currentTarget );
    let formData = form.serializeArray();

    let formParent = form.parents( '.wp-chimp-subscription-form' );
    let formFieldSet = form.children( '.wp-chimp-subscription-form__fieldset' );
    let formButton = formFieldSet.children( '.wp-chimp-subscription-form__button' );

    let apiUrl = form.attr( 'action' );

    $.ajax({
      type: 'POST',
      url: apiUrl,
      data: formData,
      beforeSend() {
        formFieldSet.prop( 'disabled', true );
        formButton.prop( 'disabled', true );
        formParent.addClass( 'is-submitting' ).fadeTo( 200, 0.5 );
      }
    });
  });
});
