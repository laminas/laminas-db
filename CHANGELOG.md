# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.8.0 - 2016-04-12

### Added

- [zendframework/zend-db#92](https://github.com/zendframework/zend-db/pull/92) adds the class
  `Laminas\Db\Sql\Join` for creating and aggregating JOIN specifications. This is
  now consumed by all `Laminas\Db\Sql` implementations in order to represent JOIN
  statements.
- [zendframework/zend-db#92](https://github.com/zendframework/zend-db/pull/92) adds support for JOIN
  operations to UPDATE statements.
- [zendframework/zend-db#92](https://github.com/zendframework/zend-db/pull/92) adds support for joins
  to `AbstractTableGateway::update`; you can now pass an array of
  specifications via a third argument to the method.
- [zendframework/zend-db#96](https://github.com/zendframework/zend-db/pull/96) exposes the package as
  config-provider/component, but adding:
  - `Laminas\Db\ConfigProvider`, which maps the `AdapterInterface` to the
    `AdapterServiceFactory`, and enables the `AdapterAbstractServiceFactory`.
  - `Laminas\Db\Module`, which does the same, for a laminas-mvc context.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.7.1 - 2016-04-12

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-db#71](https://github.com/zendframework/zend-db/pull/71) updates the `Pgsql`
  adapter to allow passing the connection charset; this can be done with the
  `charset` option when creating your adapter.
- [zendframework/zend-db#76](https://github.com/zendframework/zend-db/pull/76) fixes the behavior of
  `Laminas\Db\Sql\Insert` when an array of names is used for columns to ensure the
  string names are used, and not the array indices.
- [zendframework/zend-db#91](https://github.com/zendframework/zend-db/pull/91) fixes the behavior of
  the `Oci8` adapter when initializing a result set; previously, it was
  improperly assigning the count of affected rows to the generated value.
- [zendframework/zend-db#95](https://github.com/zendframework/zend-db/pull/95) fixes the `IbmDb2`
  platform's `quoteIdentifier()` method to properly allow `#` characters in
  identifiers (as they are commonly used on that platform).

## 2.7.0 - 2016-02-22

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-db#85](https://github.com/zendframework/zend-db/pull/85) and
  [zendframework/zend-db#87](https://github.com/zendframework/zend-db/pull/87) update the code base
  to be forwards compatible with:
  - laminas-eventmanager v3
  - laminas-hydrator v2.1
  - laminas-servicemanager v3
  - laminas-stdlib v3

## 2.6.2 - 2015-12-09

### Added

- [zendframework/zend-db#49](https://github.com/zendframework/zend-db/pull/49) Add docbook
  documentation.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-db#55](https://github.com/zendframework/zend-db/pull/55) Implement FeatureSet
  canCallMagicCall and callMagicCall methods
- [zendframework/zend-db#56](https://github.com/zendframework/zend-db/pull/56)
  AbstractResultSet::current now does validation to ensure an array.
- [zendframework/zend-db#58](https://github.com/zendframework/zend-db/pull/58) Fix unbuffered result
  on MySQLi.
- [zendframework/zend-db#59](https://github.com/zendframework/zend-db/pull/59) Allow unix_socket
  parameter

## 2.6.1 - 2015-10-14

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-db#31](https://github.com/zendframework/zend-db/pull/31) fixes table gateway
  update when there is a table alias utilized.

## 2.6.1 - 2015-10-14

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-db#43](https://github.com/zendframework/zend-db/pull/43) unset and get during
  an insert operation would throw an InvalidArgumentException during an insert.

## 2.6.0 - 2015-09-22

### Added

- [zendframework/zend-db#42](https://github.com/zendframework/zend-db/pull/42) updates the component
  to use laminas-hydrator for hydrator functionality; this provides forward
  compatibility with laminas-hydrator, and backwards compatibility with
  hydrators from older versions of laminas-stdlib.
- [zendframework/zend-db#15](https://github.com/zendframework/zend-db/pull/15) adds a new predicate,
  `Laminas\Db\Sql\Predicate\NotBetween`, which can be invoked via `Sql`
  instances: `$sql->notBetween($field, $min, $max)`.
- [zendframework/zend-db#22](https://github.com/zendframework/zend-db/pull/22) extracts a factory,
  `Laminas\Db\Metadata\Source\Factory`, from `Laminas\Db\Metadata\Metadata`,
  removing the (non-public) `createSourceFromAdapter()` method from that
  class. Additionally, it extracts `Laminas\Db\Metadata\MetadataInterface`, to
  allow creating alternate implementations.

### Deprecated

- [zendframework/zend-db#27](https://github.com/zendframework/zend-db/pull/27) deprecates the
  constants `JOIN_OUTER_LEFT` and `JOIN_OUTER_RIGHT` in favor of
  `JOIN_LEFT_OUTER` and `JOIN_RIGHT_OUTER`.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.5.2 - 2015-09-22

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-db#29](https://github.com/zendframework/zend-db/pull/29) removes the required
  second argument to `Laminas\Db\Predicate\Predicate::expression()`, allowing it to
  be nullable, and mirroring the constructor of `Laminas\Db\Predicate\Expression`.

### Fixed

- [zendframework/zend-db#40](https://github.com/zendframework/zend-db/pull/40) updates the
  laminas-stdlib dependency to reference `>=2.5.0,<2.7.0` to ensure hydrators
  will work as expected following extraction of hydrators to the laminas-hydrator
  repository.
- [zendframework/zend-db#34](https://github.com/zendframework/zend-db/pull/34) fixes retrieval of
  constraint metadata in the Oracle adapter.
- [zendframework/zend-db#41](https://github.com/zendframework/zend-db/pull/41) removes hard dependency
  on EventManager in AbstractTableGateway.
- [zendframework/zend-db#17](https://github.com/zendframework/zend-db/pull/17) removes an executable
  bit on a regular file.
- [zendframework/zend-db#3](https://github.com/zendframework/zend-db/pull/3) updates the code to use
  closure binding (now that we're on 5.5+, this is possible).
- [zendframework/zend-db#9](https://github.com/zendframework/zend-db/pull/9) thoroughly audits the
  OCI8 (Oracle) driver, ensuring it provides feature parity with other drivers,
  and fixes issues with subselects, limits, and offsets.
