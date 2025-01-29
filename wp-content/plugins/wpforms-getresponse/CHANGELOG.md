Change Log
All notable changes to this project will be documented in this file, formatted via [this recommendation](https://keepachangelog.com/).

## [1.6.0] - 2023-08-15
### Changed
- Minimum WPForms version supported is 1.8.3.

### Fixed
- The fatal error was thrown when users were trying to create a connection in the Form Builder with an expired API key.

## [1.5.1] - 2023-07-03
### Fixed
- Compatibility with WPForms 1.8.2.2.

## [1.5.0] - 2022-05-26
### IMPORTANT
- Support for PHP 5.5 has been discontinued. If you are running PHP 5.5, you MUST upgrade PHP before installing the new WPForms GetResponse and WPForms 1.7.3 (that the addon is relying on). Failure to do that will disable the WPForms GetResponse plugin.
- Support for WordPress 5.1 has been discontinued. If you are running WordPress 5.1, you MUST upgrade WordPress before installing the new WPForms GetResponse. Failure to do that will disable the new WPForms GetResponse functionality.

### Added
- Compatibility with WPForms 1.6.8 and the updated Form Builder.

### Changed
- Minimum WPForms version supported is 1.7.3.

### Fixed
- Compatibility with WordPress Multisite installations.
- Properly handle the situation when trying to change the template for the same form multiple times.
- Send to GetResponse form submission data even when the "Entry storage" option is disabled in the Form Builder.

## [1.4.0] - 2021-03-31
### Added
- Send a subscriber IP address to GetResponse if GDPR options configured to allow that.

## [1.3.0] - 2020-12-10
### IMPORTANT
- Support for PHP 5.4 and below has been discontinued. If you are running anything older than PHP 5.5, you MUST upgrade PHP before installing the WPForms GetResponse. Failure to do that will disable addon functionality.

### Added
- Updated GetResponse API from v2 to v3.
- Ability to update subscribers with data from the form submission.
- Ability to add tags to subscribers.
- Ability to define the "Day of Cycle" - Autoresponder day.
- Ability to set custom fields in GetResponse using entry data.

## [1.2.0] - 2019-07-23
### Added
- Complete translations for French and Portuguese (Brazilian).

## [1.1.0] - 2019-02-06
### Added
- Complete translations for Spanish, Italian, Japanese, and German.

### Fixed
- Typos, grammar, and other i18n related issues.

## [1.0.4] - 2018-03-15
### Fixed
- Error when adding account from Settings > Integrations tab.

## [1.0.3] - 2017-03-09
### Changed
- Adjust display order so that the providers show in alphabetical order.

## [1.0.2] - 2016-07-07
### Changed
- Improved error logging.

## [1.0.1] - 2016-06-23
### Changed
- Prevent plugin from running if WPForms Pro is not activated.

## [1.0.0] - 2016-04-26
- Initial release.
