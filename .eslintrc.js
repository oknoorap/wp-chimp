module.exports = {
  parser: 'babel-eslint',
	extends: [
		'wordpress',
		'plugin:react/recommended',
		'plugin:jsx-a11y/recommended',
		'plugin:jest/recommended'
	],
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
