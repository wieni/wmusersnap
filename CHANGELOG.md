# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.4] - 2021-10-11
### Fixed
- Fix set cookie access check

## [1.1.3] - 2021-10-11
### Fixed
- Fix existing cookies not being deleted when disabling the integration

## [1.1.2] - 2021-10-11
### Fixed
- Stop loading the widget in iframes

## [1.1.1] - 2021-09-27
### Changed
- Attach the library using `hook_page_attachments` instead of `hook_preprocess_html`. This makes it easier for other 
  modules to dynamically unload the library.

## [1.1.0] - 2021-03-15
### Added
- Add option to control whether the cookie should be removed immediately after losing access

## [1.0.0] - 2021-03-09
Initial release
