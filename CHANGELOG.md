# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.6.0 - TBD

### Added

- [#15](https://github.com/zendframework/zend-db/pull/15) adds a new predicate,
  `Zend\Db\Sql\Predicate\NotBetween`, which can be invoked via `Sql`
  instances: `$sql->notBetween($field, $min, $max)`.
- [#22](https://github.com/zendframework/zend-db/pull/22) extracts a factory,
  `Zend\Db\Metadata\Source\Factory`, from `Zend\Db\Metadata\Metadata`,
  removing the (non-public) `createSourceFromAdapter()` method from that
  class. Additionally, it extracts `Zend\Db\Metadata\MetadataInterface`, to
  allow creating alternate implementations.

### Deprecated

- [#27](https://github.com/zendframework/zend-db/pull/27) deprecates the
  constants `JOIN_OUTER_LEFT` and `JOIN_OUTER_RIGHT` in favor of
  `JOIN_LEFT_OUTER` and `JOIN_RIGHT_OUTER`.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.5.2 - TBD

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- [#29](https://github.com/zendframework/zend-db/pull/29) removes the required
  second argument to `Zend\Db\Predicate\Predicate::expression()`, allowing it to
  be nullable, and mirroring the constructor of `Zend\Db\Predicate\Expression`.

### Fixed

- [#41](https://github.com/zendframework/zend-db/pull/41) removes hard dependency
  on EventManager in AbstractTableGateway.
- [#17](https://github.com/zendframework/zend-db/pull/17) removes an executable
  bit on a regular file.
- [#3](https://github.com/zendframework/zend-db/pull/3) updates the code to use
  closure binding (now that we're on 5.5+, this is possible).
- [#9](https://github.com/zendframework/zend-db/pull/9) thoroughly audits the
  OCI8 (Oracle) driver, ensuring it provides feature parity with other drivers,
  and fixes issues with subselects, limits, and offsets.
