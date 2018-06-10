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

export function getMailChimpApiStatus() {

  var status = true;
  const { apiKey, apiKeyStatus, listsTotalItems } = wpChimpSettingsState;

  if ( ! apiKey || ! apiKeyStatus || 0 >= listsTotalItems ) {
    status = false;
  }

  return status;
}
