# Zend\\Db\\Sql\\Ddl

`Zend\Db\Sql\Ddl` is a sub-component of `Zend\Db\Sql` that allows consumers to create statement
objects that will produce DDL (Data Definition Language) SQL statements. When combined with a
platform specific `Zend\Db\Sql\Sql` object, these DDL objects are capable of producing
platform-specific `CREATE TABLE` statements, with specialized data types, constraints, and indexes
for a database/schema.

The following platforms have platform specializations for DDL:

- MySQL
- All databases compatible with ANSI SQL92

## Creating Tables

Like `Zend\Db\Sql` objects, each statement type is represented by a class. For example, `CREATE
TABLE` is modeled by a `CreateTable` object; this is likewise the same for `ALTER TABLE` (as
`AlterTable`), and `DROP TABLE` (as `DropTable`). These classes exist in the `Zend\Db\Sql\Ddl`
namespace. To initiate the building of a DDL statement, such as `CreateTable`, one needs to
instantiate the object. There are a couple of valid patterns for this:

```php
use Zend\Db\Sql\Ddl;

$table = new Ddl\CreateTable();

// or with table
$table = new Ddl\CreateTable('bar');

// optionally, as a temporary table
$table = new Ddl\CreateTable('bar', true);
```

You can also set the table after instantiation:

```php
$table->setTable('bar');
```

Currently, columns are added by creating a column object, described in the data type table in the
data type section below:

```php
use Zend\Db\Sql\Ddl\Column;
$table->addColumn(new Column\Integer('id'));
$table->addColumn(new Column\Varchar('name', 255));
```

Beyond adding columns to a table, constraints can also be added:

```php
use Zend\Db\Sql\Ddl\Constraint;
$table->addConstraint(new Constraint\PrimaryKey('id'));
$table->addConstraint(
    new Constraint\UniqueKey(['name', 'foo'], 'my_unique_key')
);
```

## Altering Tables

Similarly to `CreateTable`, you may also instantiate `AlterTable`:

```php
use Zend\Db\Sql\Ddl;

$table = new Ddl\AlterTable();

// or with table
$table = new Ddl\AlterTable('bar');

// optionally, as a temporary table
$table = new Ddl\AlterTable('bar', true);
```

The primary difference between a `CreateTable` and `AlterTable` is that the `AlterTable` takes into
account that the table and its assets already exist. Therefore, while you still have `addColumn()`
and `addConstraint()`, you will also see the ability to change existing columns:

```php
use Zend\Db\Sql\Ddl\Column;
$table->changeColumn('name', Column\Varchar('new_name', 50));
```

You may also drop existing columns or constraints:

```php
$table->dropColumn('foo');
$table->dropConstraint('my_index');
```

## Dropping Tables

To drop a table, create a `DropTable` statement object:

```php
$drop = new Ddl\DropTable('bar');
```

## Executing DDL Statements

After a DDL statement object has been created and configured, at some point you will want to execute
the statement. To do this, you will need two other objects: an `Adapter` instance, and a properly
seeded `Sql` instance.

The workflow looks something like this, with `$ddl` being a `CreateTable`, `AlterTable`, or
`DropTable` instance:

```php
use Zend\Db\Sql\Sql;

// existence of $adapter is assumed
$sql = new Sql($adapter);

$adapter->query(
    $sql->getSqlStringForSqlObject($ddl),
    $adapter::QUERY_MODE_EXECUTE
);
```

By passing the `$ddl` object through the `$sql` object's `getSqlStringForSqlObject()` method, we
ensure that any platform specific specializations/modifications are utilized to create a platform
specific SQL statement.

Next, using the constant `Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE` ensures that the SQL
statement is not prepared, as many DDL statements on a variety of platforms cannot be prepared, only
executed.

## Currently Supported Data Types

These types exist in the `Zend\Db\Sql\Ddl\Column` namespace. Data types must implement
`Zend\Db\Sql\Ddl\Column\ColumnInterface`.

In alphabetical order:

<table>
<colgroup>
<col width="18%" />
<col width="81%" />
</colgroup>
<thead>
<tr class="header">
<th align="left">Type</th>
<th align="left">Arguments For Construction</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td align="left">BigInteger</td>
<td align="left"><code>$name, $nullable = false, $default = null, array $options =
array()</code></td>
</tr>
<tr class="even">
<td align="left">Blob</td>
<td align="left"><code>$name, $length, $nullable = false, $default = null, array $options =
array()</code></td>
</tr>
<tr class="odd">
<td align="left">Boolean</td>
<td align="left"><code>$name</code></td>
</tr>
<tr class="even">
<td align="left">Char</td>
<td align="left"><code>$name, $length</code></td>
</tr>
<tr class="odd">
<td align="left">Column (generic)</td>
<td align="left"><code>$name = null</code></td>
</tr>
<tr class="even">
<td align="left">Date</td>
<td align="left"><code>$name</code></td>
</tr>
<tr class="odd">
<td align="left">Decimal</td>
<td align="left"><code>$name, $precision, $scale = null</code></td>
</tr>
<tr class="even">
<td align="left">Float</td>
<td align="left"><code>$name, $digits, $decimal</code> (Note: this class is deprecated as of 2.4.0;
use Floating instead</td>
</tr>
<tr class="odd">
<td align="left">Floating</td>
<td align="left"><code>$name, $digits, $decimal</code></td>
</tr>
<tr class="even">
<td align="left">Integer</td>
<td align="left"><code>$name, $nullable = false, $default = null, array $options =
array()</code></td>
</tr>
<tr class="odd">
<td align="left">Time</td>
<td align="left"><code>$name</code></td>
</tr>
<tr class="even">
<td align="left">Varchar</td>
<td align="left"><code>$name, $length</code></td>
</tr>
</tbody>
</table>

Each of the above types can be utilized in any place that accepts a `Column\ColumnInterface`
instance. Currently, this is primarily in `CreateTable::addColumn()` and `AlterTable`'s
`addColumn()` and `changeColumn()` methods.

## Currently Supported Constraint Types

These types exist in the `Zend\Db\Sql\Ddl\Constraint` namespace. Data types must implement
`Zend\Db\Sql\Ddl\Constraint\ConstraintInterface`.

In alphabetical order:

<table>
<colgroup>
<col width="14%" />
<col width="85%" />
</colgroup>
<thead>
<tr class="header">
<th align="left">Type</th>
<th align="left">Arguments For Construction</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td align="left">Check</td>
<td align="left"><code>$expression, $name</code></td>
</tr>
<tr class="even">
<td align="left">ForeignKey</td>
<td align="left"><code>$name, $column, $referenceTable, $referenceColumn, $onDeleteRule = null,
$onUpdateRule = null</code></td>
</tr>
<tr class="odd">
<td align="left">PrimaryKey</td>
<td align="left"><code>$columns</code></td>
</tr>
<tr class="even">
<td align="left">UniqueKey</td>
<td align="left"><code>$column, $name = null</code></td>
</tr>
</tbody>
</table>

Each of the above types can be utilized in any place that accepts a `Column\ConstraintInterface`
instance. Currently, this is primarily in `CreateTable::addConstraint()` and
`AlterTable::addConstraint()`.
