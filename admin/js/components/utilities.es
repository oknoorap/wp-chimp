
export function getApiStatus() {
  if ( 'undefined' === typeof wpApiSettings || 'undefined' === typeof wpApiSettings.root ) {
    return;
  }

  if ( -1 === wpApiSettings.root.indexOf( '/wp-json/' ) ) {
    return;
  }

  return true;
}

export function getMailChimpApiStatus( settingsState ) {

  if ( 'undefined' === typeof settingsState.mailchimp ) {
    return;
  }

  if ( false === settingsState.mailchimp.apiKey || false === settingsState.mailchimp.apiKeyStatus ) {
    return;
  }

  if ( 'invalid' === settingsState.mailchimp.apiKeyStatus ) {
    return;
  }

  return true;
}
