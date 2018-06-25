module.exports = {
  extends: 'standard',
  parserOptions: {
    ecmaVersion: 8,
    sourceType: 'module'
  },
  globals: {
    window: true,
    document: true,
    jQuery: true,
    wp: true,
    wpChimpL10n: true,
    wpChimpSettingState: true
  }
}
