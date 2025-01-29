# Changelog
All notable changes to this project will be documented in this file, formatted via [this recommendation](https://keepachangelog.com/).

## [2.5.0] - 2024-01-09
### Changed
- The minimum supported WPForms version is 1.8.6.

### Fixed
- The encoded value of the Password field did not work with custom User Login Page.
- The encoded value in the password did not work with the User Registration Form and Reset Password Form.

## [2.4.0] - 2023-11-08
### Added
- Compatibility with WPForms 1.8.5.

### Changed
- Minimum WPForms version supported is 1.8.5.

## [2.3.0] - 2023-10-24
### IMPORTANT
- Support for PHP 5.6 has been discontinued. If you are running PHP 5.6, you MUST upgrade PHP before installing WPForms User Registration 2.3.0. Failure to do that will disable WPForms User Registration functionality.
- Support for WordPress 5.4 and below has been discontinued. If you are running any of those outdated versions, you MUST upgrade WordPress before installing WPForms User Registration 2.3.0. Failure to do that will disable WPForms User Registration functionality.

### Changed
- Minimum WPForms version supported is 1.8.4.

### Fixed
- Email notifications controls in the Form Builder were misaligned.
- Compatibility with our new Calculations addon.

## [2.2.0] - 2023-08-08
### Changed
- Minimum WPForms version supported is 1.8.3.
- The `{site_name}` smart tag has been moved to the main WPForms plugin.

## [2.1.2] - 2023-07-27
### Fixed
- Compatibility with WPForms Access controls.

## [2.1.1] - 2023-07-03
### Fixed
- Username validation displayed a misleading error message when the username was automatically created from the email address.
- Compatibility with WPForms 1.8.2.2.

## [2.1.0] - 2022-08-29
### Added
- New filter `wpforms_user_registration_process_registration_get_data` that allow modifying user data before registration.

### Changed
- Validation function for a form ID when Smart Tags are processed.
- Treat empty post titles and term names in Dynamic Choices the way WordPress does.
- Minimum WPForms version supported is 1.7.6.

### Fixed
- Registration form: Conditional Logic didn't work properly.
- Activation link was missed in an email if Confirmation setting was used for Email field.

## [2.0.0] - 2021-12-09
### Added
- New Password Reset form template.
- New smart tags.
- Ability to hide a form if the user logged in.
- Registration form: enable auto-login.
- Registration form: editing for email notifications (subject and message body lines).
- Registration form: user activation message when already activated.
- Registration form: Conditional Logic support.
- New option for site administrators to resend user activation email from the Users page.
- Modern Email templates support.
- Login Form template now has the option to enable "remember me" functionality.
- Compatibility with WPForms 1.6.8 and the updated Form Builder.

### Changed
- Registration functionality is available on any form.
- Improved compatibility with WPForms Post Submissions and payment addons.
- Improved compatibility with jQuery 3.5 and no jQuery Migrate plugin.
- Improved translations by removing confusion if non-translatable placeholders are used.

### Fixed
- Sending registration emails to user/admin.
- Set an attachment author for files uploaded through a File Upload field.

## [1.3.3] - 2020-12-17
### Changed
- Enable antispam protection by default for all newly created forms using the User Login Form template.

### Fixed
- Edge case where user account would be created if late form error was registered via custom code or third-party plugin.

## [1.3.2] - 2020-08-05
### Added
- New filter around user meta processing for advanced customizing.

## [1.3.1] - 2020-03-03
### Fixed
- Incompatibility with Post Submissions addon.

## [1.3.0] - 2019-07-23
### Added
- Complete translations for French and Portuguese (Brazilian).

### Fixed
- Name field in Simple format does not pass data to user's profile.

## [1.2.0] - 2019-02-06
### Added
- Complete translations for Spanish, Italian, Japanese, and German.

### Changed
- Always show forms with Login template inside Gutenberg.

### Fixed
- Typos, grammar, and other i18n related issues.
- `nickname` user meta unable to be assigned after user registration.

## [1.1.2] - 2018-12-20
### Fixed
- Remove functions deprecated in PHP 7.2

## [1.1.1] - 2018-11-14
### Fixed
- User account created when form contains errors.

## [1.1.0] - 2018-05-14
### Fixed
- Typo in user activation email.

## [1.0.9] - 2017-12-19
### Fixed
- Login form did not set proper cookie for https:// sites.

## [1.0.8] - 2017-08-21
### Changed
- Template uses new `core` property so it displays with other core templates.

## [1.0.7] - 2017-08-01
### Fixed
- Form builder alert containing misspelling.

## [1.0.6] - 2017-02-22
### Fixed
- Capitalized letters not being allowed in custom user meta keys.

## [1.0.5] - 2016-12-08
### Changed
- Emails sent to site admin/user on account creation now use HTML email template.
- For new registration forms, the Username field is no longer required; email address used as fallback.
- Additional user data is passed to `wpforms_user_registered` action.

## [1.0.4] - 2016-10-24
### Fixed
- Setting for login form template that was not displaying.

## [1.0.3] - 2016-10-05
### Fixed
- Misnamed function causing errors.

## [1.0.2] - 2016-09-15
### Added
- Errors indicating username/email already exist are now filterable.

### Changed
- User registration and login form templates load order so it shows after default templates.

## [1.0.1] - 2016-06-23
### Added
- New filters to allow for email customizations.

### Changed
- Prevent plugin from running if WPForms Pro is not activated.

## [1.0.0] - 2016-05-19
- Initial release.
