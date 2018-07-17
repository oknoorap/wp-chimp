export const getApiRootStatus = () => {
  const { restApiUrl } = wpChimpSettingState

  if ((typeof wpChimpSettingState === 'undefined' || typeof restApiUrl === 'undefined') || !(/\/wp-json\//.test(restApiUrl))) {
    return false
  }

  return true
}
