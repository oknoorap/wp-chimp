export function getApiRootStatus() {

  var status = true;

  if ( 'undefined' === typeof wpApiSettings || 'undefined' === typeof wpApiSettings.root ) {
    status = false;
  }

  if ( -1 === wpApiSettings.root.indexOf( '/wp-json/' ) ) {
    status = false;
  }

  return status;
}
