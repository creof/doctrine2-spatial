# Change Log
All notable changes to this project will be documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased][unreleased]
### Added
- Change log file to chronicle changes.
- Stub TODO.md file.
- CONTRIBUTING.md file with guidelines.
- CrEOF\Spatial\Tests\OrmTest to removed dependency on doctrine/orm source for tests.
- Travis-CI repo hook and configuration.

### Changed
- Minimum doctrine/orm version now 2.3.
- All ORM tests now extend CrEOF\Spatial\Tests\OrmTest.
- Specifying test platform through @group annotation has been deprecated. Tests now configure supported platforms in setUp().
- Cleaned up existing test classes.
- Replaced rhumsaa/array_column dev package dependency with ramsey/array_column. Prior has been abandoned and is no longer maintained.


