export function getApiRootStatus() {

  var status = true;
  const { restApiUrl } = wpChimpSettingState;

  if ( 'undefined' === typeof wpChimpSettingState || 'undefined' === typeof restApiUrl ) {
    status = false;
  }

  if ( -1 === restApiUrl.indexOf( '/wp-json/' ) ) {
    status = false;
  }

  return status;
}
