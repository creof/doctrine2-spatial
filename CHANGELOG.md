# Change Log
All notable changes to this project will be documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added
- Added support for PostgreSql ST_MakeEnvelope function.
### Changed
- Added implementation of getTypeFamily() and getSQLType() to AbstractGeometryType.
- Rename AbstractGeometryType class to AbstractSpatialType.
- Simplify logic in isClosed() method of AbstractLineString.
- Updated copyright year in LICENSE.
### Removed
- Unused imports from a number of classes.

## [1.1] - 2015-12-20
### Added
- Local phpdocs to database platform classes.
- getMappedDatabaseTypes() method to PlatformInterface returning a unique type name used in type mapping.
- Entity and test for setting default SRID in column definition on PostgreSQL/PostGIS.
- Additional parameter to methods in PlatformInterface to pass DBAL type.
- Test class OrmMockTestCase with mocked DBAL connection.
- Test for Geography\Polygon type.
- Test for unsupported platforms.

### Changed
- Moved database platform classes to namespace CrEOF\Spatial\DBAL\Platform.
- Define exception messages where thrown in classes.
- Pass entity class names to usesEntity() in tests instead of looking them up in an array.
- Confirm types have not been previously added when setting up tests.
- Geometry and Geography platform classes unified in single class for each database platform.
- Class OrmTest renamed to OrmTestCase.
- Refactor single use methods in AbstractGeometryType into calling method.
- Include all test by default so tests are inadvertently skipped.
- Changed test class names to match filenames.

### Removed
- Static exception messages from package exception classes.
- getTypeFamily() method from PlatformInterface.
- Dependency on ramsey/array_column package.
- Empty test classes.

## [1.0.1] - 2015-12-18
### Added
- Dependency on creof/geo-parser.
- Dependency on creof/wkt-parser.
- Dependency on creof/wkb-parser.
- Additional spatial functions support for PostgreSQL/PostGIS.

### Changed
- Replace regex in AbstractPoint with parser from creof/geo-parser.
- Use parser from creof/wkt-parser in AbstractPlatform class.
- Use parser from creof/wkb-parser in AbstractPlatform class.

### Removed
- StringLexer and StringParser classes no longer needed.
- BinaryReader, BinaryParser, and Utils classes no longer needed.
- Unused expection methods from InvalidValueException.

## [1.0.0] - 2015-11-09
### Added
- Change log file to chronicle changes.
- Stub TODO.md file.
- CONTRIBUTING.md file with guidelines.
- CrEOF\Spatial\Tests\OrmTest class to remove dependency on doctrine/orm source for tests.
- Travis-CI repo hook and configuration.
- CodeClimate config.
- Test config flag "opt_mark_sql" to execute dummy query with test name before each test.
- Test config flag "opt_use_debug_stack" to use custom stack which logs queries.
- Numerous SQL/DQL functions for both PostgreSQL and MySQL.
- Coveralls config.
- MultiPolygon geometry DBAL type.

### Changed
- Minimum doctrine/orm version now 2.3.
- All ORM tests now extend CrEOF\Spatial\Tests\OrmTest.
- Specifying test platform through @group annotation has been deprecated. Tests now configure supported platforms in setUp(), unsupported tests are skipped.
- Cleaned up existing test classes.
- Replaced rhumsaa/array_column dev package dependency with ramsey/array_column. Prior has been abandoned and is no longer maintained.
- Tests now pass string values to parameters instead of objects to avoid issues with field value conversion.
- Documentation split up into multiple files.
- StringLexer and StringParser now correctly handle values with exponent/scientific notation.

### Removed
- AbstractDualGeometryDQLFunction, AbstractDualGeometryOptionalParameterDQLFunction, AbstractGeometryDQLFunction, AbstractSingleGeometryDQLFunction, AbstractTripleGeometryDQLFunction, and AbstractVariableGeometryDQLFunction classes.

