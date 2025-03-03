# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [1.2.4] - 2021-12-16

### Fixed
- Fix CHANGELOG link in readme.txt

## [1.2.3] - 2021-12-16

### Added
- Add CHANGELOG file

## [1.2.2] - 2021-12-09

### Changed
- Fix service-contracts version to avoid svn error due to PHP 8 code style


## [1.2.1] - 2021-12-09

### Changed
- Fix symfony polyfill-mbstring version to avoid wordpress svn pre-commit hook error
- Fix PHP version to 7.2 as we have to run `composer install` on a PHP 7.2 environment

## [1.2.0] - 2021-12-09

### Added
- Add end to end github actions test

### Removed
- Remove useless configuration to enable standalone mode. This mode should be entirely determined by the presence of
  an auto_prepend_file PHP directive (php.ini, Apache, nginx, ...)

### Fixed
- Fix issue that cause warning message error on front in standalone mode
- Fix behavior : bounce should not be done twice in standalone mode

## [1.1.2] - 2021-12-02

### Fixed
- Use displayErrors variable to decide if we throw error or not

## [1.1.1] - 2021-12-02

### Fixed
- Fix release script

## [1.1.0] - 2021-12-02

### Changed
- Use `0.14.0` version of crowdsec php lib
- Handle typo fixing for retro compatibility (`flex_boucing`=>`flex_bouncing` and `normal_boucing`=>`normal_bouncing`)
- Split of debug in 2 configurations : debug and display_errors

## [1.0.7] - 2021-10-22

### Added
- Add compatibility test for WordPress 5.8

## [1.0.6] - 2021-08-24

### Changed
- Handle invalid input Ip format when the scope decision is set to "Ip"

## [1.0.5] - 2021-07-01

### Changed
- Close php session after bouncing

## [1.0.4] - 2021-06-25

### Changed
- Fix a bug at install/update process of the plugin.

## [1.0.3] - 2021-06-24

### Fixed
- This release is just a small fix to let the WordPress Marketplace consider the "1.0.3" as stable and propose this
  version to be downloaded. (yes, the previous fix was not enough)

## [1.0.2] - 2021-06-24

### Fixed
- This release is just a small fix to let the WordPress Marketplace consider the "1.0.2" as stable and propose this
  version to be downloaded.


## [1.0.1] - 2021-06-24

### Changed
- Update the package metadata to indicate to the Wordpress Marketplace that this plugin has been successuly tested
with the latest Wordpress 5.7 release (PHP 7.3, 7.4, 8.0)
- Update E2E tests dependencies

### Fixed
- Fix a problem when running dev environment on linux hosts : the "enable_ipv6" docker compose attribute was no more
accepted since in docker compose v3.


## [1.0.0] - 2021-06-24

### Added

- Add Standalone mode: an option allowing the PHP engine to no longer have to load the WordPress core during the
  bouncing stage. To be able to apply this mode, the webmaster has to set the auto_prepend_file PHP flag to the
  script we provide.
- Add debug mode: user can enable the debug mode directly from the CrowdSec advanced settings panel. A more verbose log
  will be written when this flag is enabled.
- Add WordPress 5.7 support
- Add PHP 8.0 support

### Changed
- Store Plugin in a flat file. This is a step to prepare the standalone mode.
- Prevent proxies from caching the wall pages. When the WP is covered by a reverse proxy (like a CDN, Varnish, Nginx
  reverse proxy etc), the wall page (ban or catpcha) is no more cached.


### Fixed
- Fix incompatibilities with other plugin (session_start). When another plugin uses PHP sessions, using the two
  plugins together trigger a PHP notice (session_start already sent). This has been fixed.


## [0.6.0] - 2021-01-23

### Added
- Add ipv6 support

## [0.5.4] - 2021-01-14

### Changed
- Update doc

## [0.5.3] - 2021-01-14

### Changed
- Update doc and assets

## [0.5.2] - 2021-01-14

### Changed
- Update doc and assets


## [0.5.1] - 2021-01-14

### Changed
- Update doc and assets

## [0.5.0] - 2021-01-13

### Changed
- Allow user to customize public pages

## [0.4.5] - 2021-01-12

### Changed
- Update deps
- Use `.env` file for docker-compose
- Update doc

## [0.4.4] - 2021-01-12

### Changed
- Improve dev environment

## [0.4.3] - 2021-01-05

### Changed
- Improve log system

## [0.4.2] - 2021-01-05

### Changed
- Improve security

## [0.4.1] - 2020-12-26

### Added
- Add more tests


## [0.4.0] - 2020-12-24

### Added
- Add cdn ip ranges
- Add WordPress support from 4.9 to 5.6
- Add functional tests for every WordPress version
- Add wp scan dev tool


## [0.3.0] - 2020-12-22

### Added
- Add redis and memcached connection checks
- Make a lint pass


## [0.2.0] - 2020-12-22

### Added

- Use the new bouncer constructor syntax
- Allow hiding cs mentions
- Remove todo mentions
- Hide paranoid mode as it is wip
- Add versioning process

## [0.1.0] - 2020-12-22

### Added

- Initial release





















