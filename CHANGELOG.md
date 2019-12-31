# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

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
