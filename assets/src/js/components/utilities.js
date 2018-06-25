export const getApiRootStatus = () => {
  let status = true
  const { restApiUrl } = wpChimpSettingState

  if ((typeof wpChimpSettingState === 'undefined' || typeof restApiUrl === 'undefined') || !(/\/wp-json\//.test(restApiUrl))) {
    status = false
  }

  return status
}
