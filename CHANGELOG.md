# Changelog
All notable changes to this project will be documented in this file.

## [2.0.0] - 2018-11-12

### Changed
- renamed tl_privacy_protocol_entry.table to tl_privacy_protocol_entry.dataContainer
**Caution: Depending on your implementation this may be breaking!**
- added a migration command to the dca to rename the table while keeping the data (we can't rely on the contao databaseupdater here)
- enhanced english translations

## [1.8.1] - 2018-10-30

### Added
- Italian translation (thanks @MicioMax)

## [1.8.0] - 2018-10-17

### Added
- custom label parameter for ProtocolManager::getSelectorFieldDca()

## [1.7.2] - 2018-10-16

### Fixed
- submission data for reference entity update

## [1.7.1] - 2018-09-21

### Fixed
- backend opt-in language issue
- settings palette issue

## [1.7.0] - 2018-09-19

### Added
- support for heimrichhannot/contao-privacy-api-bundle

### Changed
- refactoring on palette
- refactoring on method usage and fields

## [1.6.3] - 2018-09-19

### Fixed
- wrong foreign key comparison 
- missing template in contao 3
- error due filter flag for table row in log entries

### Updated
- enhanced foreign key display in protocal archive form

## [1.6.2] - 2018-09-18

### Fixed
- opt in issues

## [1.6.1] - 2018-09-17

### Fixed
- opt in issues

## [1.6.0] - 2018-08-29

### Changed
- dropped strong `heimrichhannot/contao-exporter` requirement, and suggest `heimrichhannot/contao-exporter` (contao 3) or `heimrichhannot/contao-exporter-bundle` (contao 4)

## [1.5.0] - 2018-08-21

### Fixed

- privacy opt in emails for supporting multiple languages

## [1.4.0] - 2018-08-01

### Fixed

- refactored opt module and insert tags

## [1.3.1] - 2018-07-24

### Fixed

- callback protocol creation

## [1.3.0] - 2018-07-23

### Added

- agreement field

## [1.2.1] - 2018-06-29

### Fixed

- Readme

## [1.2.0] - 2018-06-26

### Added

- ModuleProtocolEntryEditor for opt-in and opt-out
- academicTitle and gender for protocol
- backend form for sending opt-in emails

## [1.1.3] - 2018-06-22

### Fixed

- protocol member and user inconsistencies
- dca issues

## [1.1.2] - 2018-06-15

### Added

- type to skipFields for protocol

## [1.1.1] - 2018-06-15

### Changed

- localization

## [1.1.0] - 2018-06-08

### Added

- author field for protocol entries, fixed cmsScope inconsistencies

## [1.0.1] - 2018-05-24

### Added

- shorthands for adding module fields
