# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.3.0] - 2014-07-25

### Fixed

- Object Caching compatibility with the plugin options (#34).
- Handle the Exception for when the MailChimp API key format is incorrect (#33).

### Added

- Object Caching layer to cache the MailChimp lists query.
- `Core\Settings` class to handle the plugin settings or state.
- `Core\Plugin_Base` as the base Class for some of the plugin Classes with shared properties and methods.
- `Core\Endpoints\REST_Controller` as the base Class to create custom endpoint API in the plugin.

### Changed

- The WordPress hooks loader is now run from within its own Class (#35).

## [0.2.1] - 2014-07-20

### Changed

- PHP and WordPress version requirement is now less strict (5.4 instead of 5.4.45 and 4.9 instead of 4.9.0) (#32).

### Fixed

- Reset the default list option when MailChimp API key is added or updated (#31).

## [0.2.0] - 2014-07-17

### Added

- A pre-commit hook to analyze code before commiting changes to Git (#26).
- A new class called `Core\Options` to add, update, and validate the plugin options.

### Changed

- The MailChimp API key that's shown in the Settings page is now obfuscated (#29).

### Fixed

- Slow WordPress `get_option` querying the plugin options.
- Duplicated lists query in the plugin Settings page.
- Incorrect endpoint URL to resync the lists.

## 0.1.0 - 2018-06-03

### Added

- Basic functionality of a MailChimp subscription form.
- A custom basic Gutenberg block interface to add and edit the subscription form.
- A Widget to add and edit the subscription form.
- A Settings page for the plugin.
- A couple of custom WP-API endpoints.

[Unreleased]: https://github.com/wp-chimp/wp-chimp/compare/v0.3.0...HEAD
[0.3.0]: https://github.com/wp-chimp/wp-chimp/compare/v0.2.1...v0.3.0
[0.2.1]: https://github.com/wp-chimp/wp-chimp/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/wp-chimp/wp-chimp/compare/v0.1.0...v0.2.0
