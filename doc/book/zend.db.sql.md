# Zend\\Db\\Sql

`Zend\Db\Sql` is a SQL abstraction layer for building platform specific SQL queries via an
object-oriented API. The end result of an `Zend\Db\Sql` object will be to either produce a Statement
and Parameter container that represents the target query, or a full string that can be directly
executed against the database platform. To achieve this, `Zend\Db\Sql` objects require a
`Zend\Db\Adapter\Adapter` object in order to produce the desired results.

## Zend\\Db\\Sql\\Sql (Quickstart)

As there are four primary tasks associated with interacting with a database (from the DML, or Data
Manipulation Language): selecting, inserting, updating and deleting. As such, there are four primary
objects that developers can interact or building queries, `Zend\Db\Sql\Select`, `Insert`, `Update`
and `Delete`.

Since these four tasks are so closely related, and generally used together within the same
application, `Zend\Db\Sql\Sql` objects help you create them and produce the result you are
attempting to achieve.

```php
use Zend\Db\Sql\Sql;

$sql    = new Sql($adapter);
$select = $sql->select(); // @return Zend\Db\Sql\Select
$insert = $sql->insert(); // @return Zend\Db\Sql\Insert
$update = $sql->update(); // @return Zend\Db\Sql\Update
$delete = $sql->delete(); // @return Zend\Db\Sql\Delete
```

As a developer, you can now interact with these objects, as described in the sections below, to
specialize each query. Once they have been populated with values, they are ready to either be
prepared or executed.

To prepare (using a Select object):

```php
use Zend\Db\Sql\Sql;

$sql    = new Sql($adapter);
$select = $sql->select();
$select->from('foo');
$select->where(['id' => 2]);

$statement = $sql->prepareStatementForSqlObject($select);
$results = $statement->execute();
```

To execute (using a Select object)

```php
use Zend\Db\Sql\Sql;

$sql    = new Sql($adapter);
$select = $sql->select();
$select->from('foo');
$select->where(['id' => 2]);

$selectString = $sql->buildSqlString($select);
$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
```

`Zend\\Db\\Sql\\Sql` objects can also be bound to a particular table so that in getting a select,
insert, update, or delete object, they are all primarily seeded with the same table when produced.

```php
use Zend\Db\Sql\Sql;

$sql    = new Sql($adapter, 'foo');
$select = $sql->select();
$select->where(['id' => 2]); // $select already has the from('foo') applied
```

## Zend\\Db\\Sql's Select, Insert, Update and Delete

Each of these objects implements the following (2) interfaces:

```php
interface PreparableSqlInterface
{
     public function prepareStatement(Adapter $adapter, StatementInterface $statement);
}

interface SqlInterface
{
     public function getSqlString(PlatformInterface $adapterPlatform = null);
}
```

These are the functions you can call to either produce (a) a prepared statement, or (b) a string to
be executed.

## Zend\\Db\\Sql\\Select

`Zend\Db\Sql\Select` is an object with the primary function of presenting a unified API for building
platform specific SQL SELECT queries. The class can be instantiated and consumed without
`Zend\Db\Sql\Sql`:

```php
use Zend\Db\Sql\Select;

$select = new Select();
// or, to produce a $select bound to a specific table
$select = new Select('foo');
```

If a table is provided to the Select object, then `from()` cannot be called later to change the name
of the table.

Once you have a valid Select object, the following API can be used to further specify various select
statement parts:

```php
class Select extends AbstractSql implements SqlInterface, PreparableSqlInterface
{
    const JOIN_INNER = 'inner';
    const JOIN_OUTER = 'outer';
    const JOIN_LEFT = 'left';
    const JOIN_RIGHT = 'right';
    const SQL_STAR = '*';
    const ORDER_ASCENDING = 'ASC';
    const ORDER_DESCENDING = 'DESC';

    public $where; // @param Where $where

    public function __construct($table = null);
    public function from($table);
    public function columns(array $columns, $prefixColumnsWithTable = true);
    public function join($name, $on, $columns = self::SQL_STAR, $type = self::JOIN_INNER);
    public function where($predicate, $combination = Predicate\PredicateSet::OP_AND);
    public function group($group);
    public function having($predicate, $combination = Predicate\PredicateSet::OP_AND);
    public function order($order);
    public function limit($limit);
    public function offset($offset);
}
```

### from():

```php
// as a string:
$select->from('foo');

// as an array to specify an alias:
// produces SELECT "t".* FROM "table" AS "t"

$select->from(['t' => 'table']);

// using a Sql\TableIdentifier:
// same output as above

$select->from(new TableIdentifier(['t' => 'table']));
```

### columns():

```php
// as array of names
$select->columns(['foo', 'bar']);

// as an associative array with aliases as the keys:
// produces 'bar' AS 'foo', 'bax' AS 'baz'

$select->columns(['foo' => 'bar', 'baz' => 'bax']);
```

### join():

```php
$select->join(
    'foo', // table name
    'id = bar.id', // expression to join on (will be quoted by platform object before insertion),
    ['bar', 'baz'], // (optional) list of columns, same requirements as columns() above
    $select::JOIN_OUTER // (optional), one of inner, outer, left, right also represented by constants in the API
);

$select
    ->from(['f' => 'foo'])     // base table
    ->join(
        ['b' => 'bar'],        // join table with alias
        'f.foo_id = b.foo_id'  // join expression
    );
```

### where(), having():

The `Zend\Db\Sql\Select` object provides bit of flexibility as it regards to what kind of parameters
are acceptable when calling where() or having(). The method signature is listed as:

```php
/**
 * Create where clause
 *
 * @param  Where|\Closure|string|array $predicate
 * @param  string $combination One of the OP_* constants from Predicate\PredicateSet
 * @return Select
 */
public function where($predicate, $combination = Predicate\PredicateSet::OP_AND);
```

As you can see, there are a number of different ways to pass criteria to both having() and where().

If you provide a `Zend\Db\Sql\Where` object to where() or a `Zend\Db\Sql\Having` object to having(),
the internal objects for Select will be replaced completely. When the where/having() is processed,
this object will be iterated to produce the WHERE or HAVING section of the SELECT statement.

If you provide a `Closure` to where() or having(), this function will be called with the Select's
`Where` object as the only parameter. So the following is possible:

```php
$spec = function (Where $where) {
    $where->like('username', 'ralph%');
};

$select->where($spec);
```

If you provide a string, this string will be used to instantiate a
`Zend\Db\Sql\Predicate\Expression` object so that it's contents will be applied as is. This means
that there will be no quoting in the fragment provided.

Consider the following code:

```php
// SELECT "foo".* FROM "foo" WHERE x = 5
$select->from('foo')->where('x = 5');
```

If you provide an array who's values are keyed by an integer, the value can either be a string that
will be then used to build a `Predicate\Expression` or any object that implements
`Predicate\PredicateInterface`. These objects are pushed onto the Where stack with the $combination
provided.

Consider the following code:

```php
// SELECT "foo".* FROM "foo" WHERE x = 5 AND y = z
$select->from('foo')->where(['x = 5', 'y = z']);
```

If you provide an array with values keyed as strings, these values will be handled in the
following:

- PHP value nulls will be made into a `Predicate\IsNull` object
- PHP value array()s will be made into a `Predicate\In` object
- PHP value strings will be made into a `Predicate\Operator` object such that the string key will be
  identifier, and the value will target value.

Consider the following code:

```php
// SELECT "foo".* FROM "foo" WHERE "c1" IS NULL AND "c2" IN (?, ?, ?) AND "c3" IS NOT NULL
$select->from('foo')->where([
    'c1' => null,
    'c2' => [1, 2, 3],
    new \Zend\Db\Sql\Predicate\IsNotNull('c3'),
]);
```

### order():

```php
$select = new Select;
$select->order('id DESC'); // produces 'id' DESC

$select = new Select;
$select
    ->order('id DESC')
    ->order('name ASC, age DESC'); // produces 'id' DESC, 'name' ASC, 'age' DESC

$select = new Select;
$select->order(['name ASC', 'age DESC']); // produces 'name' ASC, 'age' DESC
```

### limit() and offset():

```php
$select = new Select;
$select->limit(5); // always takes an integer/numeric
$select->offset(10); // similarly takes an integer/numeric
```

## Zend\\Db\\Sql\\Insert

The Insert API:

```php
class Insert implements SqlInterface, PreparableSqlInterface
{
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';

    public function __construct($table = null);
    public function into($table);
    public function columns(array $columns);
    public function values(array $values, $flag = self::VALUES_SET);
}
```

Similarly to Select objects, the table can be set at construction time or via into().

### columns():

```php
$insert->columns(['foo', 'bar']); // set the valid columns
```

### values():

```php
// default behavior of values is to set the values
// successive calls will not preserve values from previous calls
$insert->values([
    'col_1' => 'value1',
    'col_2' => 'value2',
]);
```

```php
// merging values with previous calls
$insert->values(['col_2' => 'value2'], $insert::VALUES_MERGE);
```

## Zend\\Db\\Sql\\Update

```php
class Update
{
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';

    public $where; // @param Where $where
    public function __construct($table = null);
    public function table($table);
    public function set(array $values, $flag = self::VALUES_SET);
    public function where($predicate, $combination = Predicate\PredicateSet::OP_AND);
}
```

### set():

```php
$update->set(['foo' => 'bar', 'baz' => 'bax']);
```

### where():

See where section below.

## Zend\\Db\\Sql\\Delete

```php
class Delete
{
    public $where; // @param Where $where
    public function __construct($table = null);
    public function from($table);
    public function where($predicate, $combination = Predicate\PredicateSet::OP_AND);
}
```

### where():

See where section below.

## Zend\\Db\\Sql\\Where & Zend\\Db\\Sql\\Having

In the following, we will talk about Where, Having is implies as being the same API.

Effectively, Where and Having extend from the same base object, a Predicate (and PredicateSet). All
of the parts that make up a where or having that are and'ed or or'd together are called predicates.
The full set of predicates is called a PredicateSet. This object set generally contains the values
(and identifiers) separate from the fragment they belong to until the last possible moment when the
statement is either used to be prepared (parameteritized), or executed. In parameterization, the
parameters will be replaced with their proper placeholder (a named or positional parameter), and the
values stored inside a Adapter\\ParameterContainer. When executed, the values will be interpolated
into the fragments they belong to and properly quoted.

It is important to know that in this API, a distinction is made between what elements are considered
identifiers (TYPE\_IDENTIFIER) and which of those is a value (TYPE\_VALUE). There is also a special
use case type for literal values (TYPE\_LITERAL). These are all exposed via the
`Zend\Db\Sql\ExpressionInterface` interface.

> ### Literals
>
> In ZF 2.1, an actual `Literal` type was added. `Zend\Db\Sql` now makes the distinction that Literals
> will not have any parameters that need interpolating whereas it is expected that `Expression`
> objects *might* have parameters that need interpolating. In cases where there are parameters in an
> `Expression`, `Zend\Db\Sql\AbstractSql` will do its best to identify placeholders when the
> Expression is processed during statement creation. In short, if you don't have parameters, use
> `Literal` objects.

The Zend\\Db\\Sql\\Where (Predicate/PredicateSet) API:

```php
// Where & Having:
class Predicate extends PredicateSet
{
    public $and;
    public $or;
    public $AND;
    public $OR;
    public $NEST;
    public $UNNEST;

    public function nest();
    public function setUnnest(Predicate $predicate);
    public function unnest();
    public function equalTo(
        $left,
        $right,
        $leftType = self::TYPE_IDENTIFIER,
        $rightType = self::TYPE_VALUE
    );
    public function notEqualTo(
        $left,
        $right,
        $leftType = self::TYPE_IDENTIFIER,
        $rightType = self::TYPE_VALUE
    );
    public function lessThan(
        $left,
        $right,
        $leftType = self::TYPE_IDENTIFIER,
        $rightType = self::TYPE_VALUE
    );
    public function greaterThan(
        $left,
        $right,
        $leftType = self::TYPE_IDENTIFIER,
        $rightType = self::TYPE_VALUE
    );
    public function lessThanOrEqualTo(
        $left,
        $right,
        $leftType = self::TYPE_IDENTIFIER,
        $rightType = self::TYPE_VALUE
    );
    public function greaterThanOrEqualTo(
        $left,
        $right,
        $leftType = self::TYPE_IDENTIFIER,
        $rightType = self::TYPE_VALUE
    );
    public function like($identifier, $like);
    public function literal($literal);
    public function expression($expression, $parameter);
    public function isNull($identifier);
    public function isNotNull($identifier);
    public function in($identifier, array $valueSet = []);
    public function between($identifier, $minValue, $maxValue);

    // Inherited From PredicateSet

    public function addPredicate(PredicateInterface $predicate, $combination = null);
    public function getPredicates();
    public function orPredicate(PredicateInterface $predicate);
    public function andPredicate(PredicateInterface $predicate);
    public function getExpressionData();
    public function count();
}
```

Each method in the Where API will produce a corresponding Predicate object of a similarly named
type, described below, with the full API of the object:

### equalTo(), lessThan(), greaterThan(), lessThanOrEqualTo(), greaterThanOrEqualTo():

```php
$where->equalTo('id', 5);

// same as the following workflow
$where->addPredicate(
    new Predicate\Operator($left, Operator::OPERATOR_EQUAL_TO, $right, $leftType, $rightType)
);

class Operator implements PredicateInterface
{
    const OPERATOR_EQUAL_TO                  = '=';
    const OP_EQ                              = '=';
    const OPERATOR_NOT_EQUAL_TO              = '!=';
    const OP_NE                              = '!=';
    const OPERATOR_LESS_THAN                 = '<';
    const OP_LT                              = '<';
    const OPERATOR_LESS_THAN_OR_EQUAL_TO     = '<=';
    const OP_LTE                             = '<=';
    const OPERATOR_GREATER_THAN              = '>';
    const OP_GT                              = '>';
    const OPERATOR_GREATER_THAN_OR_EQUAL_TO  = '>=';
    const OP_GTE                             = '>=';

    public function __construct(
        $left = null,
        $operator = self::OPERATOR_EQUAL_TO,
        $right = null,
        $leftType = self::TYPE_IDENTIFIER,
        $rightType = self::TYPE_VALUE
    );
    public function setLeft($left);
    public function getLeft();
    public function setLeftType($type);
    public function getLeftType();
    public function setOperator($operator);
    public function getOperator();
    public function setRight($value);
    public function getRight();
    public function setRightType($type);
    public function getRightType();
    public function getExpressionData();
}
```

### like($identifier, $like):

```php
$where->like($identifier, $like):

// same as
$where->addPredicate(
    new Predicate\Like($identifier, $like)
);

// full API

class Like implements PredicateInterface
{
    public function __construct($identifier = null, $like = null);
    public function setIdentifier($identifier);
    public function getIdentifier();
    public function setLike($like);
    public function getLike();
}
```

### literal($literal);

```php
$where->literal($literal);

// same as
$where->addPredicate(
    new Predicate\Literal($literal)
);

// full API
class Literal implements ExpressionInterface, PredicateInterface
{
    const PLACEHOLDER = '?';
    public function __construct($literal = '');
    public function setLiteral($literal);
    public function getLiteral();
}
```

### expression($expression, $parameter);

```php
$where->expression($expression, $parameter);

// same as
$where->addPredicate(
    new Predicate\Expression($expression, $parameter)
);

// full API
class Expression implements ExpressionInterface, PredicateInterface
{
    const PLACEHOLDER = '?';

    public function __construct(
        $expression = null,
        $valueParameter = null
        /* [, $valueParameter, ...  ] */
    );
    public function setExpression($expression);
    public function getExpression();
    public function setParameters($parameters);
    public function getParameters();
    public function setTypes(array $types);
    public function getTypes();
}
```

### isNull($identifier);

```php
$where->isNull($identifier);

// same as
$where->addPredicate(
    new Predicate\IsNull($identifier)
);

// full API
class IsNull implements PredicateInterface
{
    public function __construct($identifier = null);
    public function setIdentifier($identifier);
    public function getIdentifier();
}
```

### isNotNull($identifier);

```php
$where->isNotNull($identifier);

// same as
$where->addPredicate(
    new Predicate\IsNotNull($identifier)
);

// full API
class IsNotNull implements PredicateInterface
{
    public function __construct($identifier = null);
    public function setIdentifier($identifier);
    public function getIdentifier();
}
```

### in($identifier, array $valueSet = []);

```php
$where->in($identifier, array $valueSet = []);

// same as
$where->addPredicate(
    new Predicate\In($identifier, $valueSet)
);

// full API
class In implements PredicateInterface
{
    public function __construct($identifier = null, array $valueSet = []);
    public function setIdentifier($identifier);
    public function getIdentifier();
    public function setValueSet(array $valueSet);
    public function getValueSet();
}
```

### between($identifier, $minValue, $maxValue);

```php
$where->between($identifier, $minValue, $maxValue);

// same as
$where->addPredicate(
    new Predicate\Between($identifier, $minValue, $maxValue)
);

// full API
class Between implements PredicateInterface
{
    public function __construct($identifier = null, $minValue = null, $maxValue = null);
    public function setIdentifier($identifier);
    public function getIdentifier();
    public function setMinValue($minValue);
    public function getMinValue();
    public function setMaxValue($maxValue);
    public function getMaxValue();
    public function setSpecification($specification);
}
```
