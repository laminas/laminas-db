<?php

namespace LaminasTest\Db\Sql\Platform\Mysql;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Mysqli\Statement;
use Laminas\Db\Adapter\Driver\Pdo\Connection;
use Laminas\Db\Adapter\Driver\Pdo\Pdo;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\Mysql as MysqlPlatform;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Platform\Mysql\SelectDecorator;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use LaminasTest\Db\TestAsset\TrustingMysqlPlatform;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SelectDecoratorTest extends TestCase
{
    /** @var Adapter&MockObject */
    protected $mockAdapter;

    /** @var Sql */
    protected $sql;
    /**
     * @testdox integration test: Testing SelectDecorator will use Select an internal state to prepare
     *                            a proper limit/offset sql statement
     * @covers \Laminas\Db\Sql\Platform\Mysql\SelectDecorator::prepareStatement
     * @covers \Laminas\Db\Sql\Platform\Mysql\SelectDecorator::processLimit
     * @covers \Laminas\Db\Sql\Platform\Mysql\SelectDecorator::processOffset
     * @dataProvider dataProvider
     */
    public function testPrepareStatement(Select $select, string $expectedSql, array $expectedParams)
    {
        $driver = $this->getMockBuilder(DriverInterface::class)->getMock();
        $driver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));

        // test
        $adapter = $this->getMockBuilder(Adapter::class)
            ->setMethods()
            ->setConstructorArgs([
                $driver,
                new MysqlPlatform(),
            ])
            ->getMock();

        $parameterContainer = new ParameterContainer();
        $statement          = $this->getMockBuilder(StatementInterface::class)->getMock();
        $statement->expects($this->any())->method('getParameterContainer')
            ->will($this->returnValue($parameterContainer));

        $statement->expects($this->once())->method('setSql')->with($expectedSql);

        $selectDecorator = new SelectDecorator();
        $selectDecorator->setSubject($select);
        $selectDecorator->prepareStatement($adapter, $statement);

        self::assertEquals($expectedParams, $parameterContainer->getNamedArray());
    }

    /**
     * @testdox integration test: Testing SelectDecorator will use Select an internal state to prepare
     *                            a proper limit/offset sql statement
     * @covers \Laminas\Db\Sql\Platform\Mysql\SelectDecorator::prepareStatement
     * @covers \Laminas\Db\Sql\Platform\Mysql\SelectDecorator::processLimit
     * @covers \Laminas\Db\Sql\Platform\Mysql\SelectDecorator::processOffset
     * @dataProvider dataProvider
     * @param mixed $ignore
     * @param array<string, mixed> $params
     * @param mixed $alsoIgnore
     */
    public function testPrepareStatementForSqlObject(
        Select $select,
        $ignore,
        array $expectedParams,
        $alsoIgnore,
        string $expectedPdoSql
    ) {
        // mock the adapter, driver, and parts
        $newStatement     = new Statement();
        $driver           = new Pdo(new Connection());
        $mockAdapter      = $this->getMockBuilder(Adapter::class)
                            ->setConstructorArgs([$driver, new TrustingMysqlPlatform()])
                            ->getMock();
        $trustingPlatform = new TrustingMysqlPlatform();
        $mockAdapter->expects($this->any())->method('getPlatform')->will($this->returnValue($trustingPlatform));
        $mockAdapter->expects($this->any())->method('getDriver')->will($this->returnValue($driver));
        // setup mock adapter
        $this->mockAdapter = $mockAdapter;

        $this->sql       = new Sql($this->mockAdapter, 'foo');
        $selectDecorator = new SelectDecorator();
        $selectDecorator->setSubject($select);
        $statement = $this->sql->prepareStatementForSqlObject($select, $newStatement);
        self::assertEquals($expectedPdoSql, $statement->getSql());
    }

    /**
     * @testdox integration test: Testing SelectDecorator will use Select an internal state to prepare
     *                            a proper limit/offset sql statement
     * @covers \Laminas\Db\Sql\Platform\Mysql\SelectDecorator::getSqlString
     * @covers \Laminas\Db\Sql\Platform\Mysql\SelectDecorator::processLimit
     * @covers \Laminas\Db\Sql\Platform\Mysql\SelectDecorator::processOffset
     * @dataProvider dataProvider
     * @param mixed $ignore
     * @param mixed $alsoIgnore
     */
    public function testGetSqlString(Select $select, $ignore, $alsoIgnore, string $expectedSql)
    {
        $parameterContainer = new ParameterContainer();
        $statement          = $this->getMockBuilder(StatementInterface::class)->getMock();
        $statement->expects($this->any())->method('getParameterContainer')
            ->will($this->returnValue($parameterContainer));

        $selectDecorator = new SelectDecorator();
        $selectDecorator->setSubject($select);
        self::assertEquals($expectedSql, $selectDecorator->getSqlString(new TrustingMysqlPlatform()));
    }

    /**
     * @psalm-return array<array-key, array{
     *     0: Select,
     *     1: string,
     *     2: array<string, mixed>
     *     3: string,
     *     4: string
     * }>
     */
    public function dataProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        $select0 = new Select();
        $select0->from('foo')->limit(5)->offset(10);
        $expectedPrepareSql0       = 'SELECT `foo`.* FROM `foo` LIMIT ? OFFSET ?';
        $expectedPrepareObjectSql0 = 'SELECT `foo`.* FROM `foo` LIMIT :limit OFFSET :offset';
        $expectedParams0           = ['offset' => 10, 'limit' => 5];
        $expectedSql0              = 'SELECT `foo`.* FROM `foo` LIMIT 5 OFFSET 10';

        // offset without limit
        $select1 = new Select();
        $select1->from('foo')->offset(10);
        $expectedPrepareSql1       = 'SELECT `foo`.* FROM `foo` LIMIT 18446744073709551615 OFFSET ?';
        $expectedPrepareObjectSql1 = 'SELECT `foo`.* FROM `foo` LIMIT 18446744073709551615 OFFSET :offset';
        $expectedParams1           = ['offset' => 10];
        $expectedSql1              = 'SELECT `foo`.* FROM `foo` LIMIT 18446744073709551615 OFFSET 10';

        // offset and limit are not type casted when injected into parameter container
        $select2 = new Select();
        $select2->from('foo')->limit('5')->offset('10000000000000000000');
        $expectedPrepareSql2       = 'SELECT `foo`.* FROM `foo` LIMIT ? OFFSET ?';
        $expectedPrepareObjectSql2 = 'SELECT `foo`.* FROM `foo` LIMIT :limit OFFSET :offset';
        $expectedParams2           = ['offset' => '10000000000000000000', 'limit' => '5'];
        $expectedSql2              = 'SELECT `foo`.* FROM `foo` LIMIT 5 OFFSET 10000000000000000000';

        // nested single limit & offset in field param
        $nestedSelect0 = new Select();
        $nestedSelect0->from('foo1')
            ->columns([
                'cnt' => new Expression('count(foo1.id)'),
            ])->limit(100)->offset(500);

        $select3 = new Select();
        $select3->from('foo')
            ->columns([
                'res' => $nestedSelect0,
            ])
            ->limit(10)->offset(50);

        $expectedPrepareSql3       =
            "SELECT (SELECT count(foo1.id) AS `cnt` FROM `foo1` LIMIT ? OFFSET ?) AS `res`"
            . " FROM `foo` LIMIT ? OFFSET ?";
        $expectedPrepareObjectSql3 =
        "SELECT (SELECT count(foo1.id) AS `cnt` FROM `foo1` LIMIT :subselect1limit OFFSET :subselect1offset) AS `res`"
        . " FROM `foo` LIMIT :limit OFFSET :offset";
        $expectedParams3           = [
            'subselect1limit'  => 100,
            'subselect1offset' => 500,
            'limit'            => 10,
            'offset'           => 50,
        ];
        $expectedSql3              = "SELECT (SELECT count(foo1.id) AS `cnt`"
            . " FROM `foo1` LIMIT 100 OFFSET 500) AS `res`"
            . " FROM `foo` LIMIT 10 OFFSET 50";

        // nested multiple limit & offset in field param
        $nestedSelect0 = new Select();
        $nestedSelect0->from('foo1')
            ->columns([
                'cnt' => new Expression('count(foo1.id)'),
            ])->limit(100)->offset(500);

        $nestedSelect1 = new Select();
        $nestedSelect1->from('foo2')
            ->columns([
                'cnt' => new Expression('count(foo2.id)'),
            ])->limit(50)->offset(101);

        $select4 = new Select();
        $select4->from('foo')
            ->columns([
                'res'  => $nestedSelect0,
                'res0' => $nestedSelect1,
            ])
            ->limit(10)->offset(5);

        $expectedPrepareSql4       =
            "SELECT (SELECT count(foo1.id) AS `cnt` FROM `foo1` LIMIT ? OFFSET ?) AS `res`,"
            . " (SELECT count(foo2.id) AS `cnt` FROM `foo2` LIMIT ? OFFSET ?) AS `res0`"
            . " FROM `foo` LIMIT ? OFFSET ?";
        $expectedPrepareObjectSql4 =
            "SELECT (SELECT count(foo1.id) AS `cnt` FROM `foo1` LIMIT :subselect1limit OFFSET :subselect1offset)"
            . " AS `res`, (SELECT count(foo2.id) AS `cnt` FROM `foo2` LIMIT :subselect2limit OFFSET :subselect2offset)"
            . " AS `res0` FROM `foo` LIMIT :limit OFFSET :offset";
        $expectedParams4           = [
            'subselect1limit'  => 100,
            'subselect1offset' => 500,
            'subselect2limit'  => 50,
            'subselect2offset' => 101,
            'limit'            => 10,
            'offset'           => 5,
        ];
        $expectedSql4              = "SELECT (SELECT count(foo1.id) AS `cnt` FROM `foo1` LIMIT 100 OFFSET 500) AS `res`,"
            . " (SELECT count(foo2.id) AS `cnt` FROM `foo2` LIMIT 50 OFFSET 101) AS `res0`"
            . " FROM `foo` LIMIT 10 OFFSET 5";

        // nested limit in field param, no limit in containing select
        $nestedSelect0 = new Select();
        $nestedSelect0->from('foo1')
            ->columns([
                'cnt' => new Expression('count(foo1.id)'),
            ]);
        $nestedSelect0->where->equalTo('foo2', 'ab');
        $nestedSelect0->limit(1);

        $select5 = new Select();
        $select5->from('foo')
            ->columns([
                'res' => $nestedSelect0,
            ]);

        $expectedPrepareSql5       =
            "SELECT (SELECT count(foo1.id) AS `cnt` FROM `foo1` WHERE `foo2` = ? LIMIT ?) AS `res`"
            . " FROM `foo`";
        $expectedPrepareObjectSql5 =
            "SELECT (SELECT count(foo1.id) AS `cnt` FROM `foo1` WHERE `foo2` = :subselect1where1 LIMIT"
            . " :subselect1limit) AS `res` FROM `foo`";
        $expectedParams5           = [
            'subselect1limit'  => 1,
            'subselect1where1' => 'ab',
        ];
        $expectedSql5              = "SELECT (SELECT count(foo1.id) AS `cnt`"
            . " FROM `foo1` WHERE `foo2` = 'ab' LIMIT 1) AS `res`"
            . " FROM `foo`";

        return [
            [$select0, $expectedPrepareSql0, $expectedParams0, $expectedSql0, $expectedPrepareObjectSql0],
            [$select1, $expectedPrepareSql1, $expectedParams1, $expectedSql1, $expectedPrepareObjectSql1],
            [$select2, $expectedPrepareSql2, $expectedParams2, $expectedSql2, $expectedPrepareObjectSql2],
            [$select3, $expectedPrepareSql3, $expectedParams3, $expectedSql3, $expectedPrepareObjectSql3],
            [$select4, $expectedPrepareSql4, $expectedParams4, $expectedSql4, $expectedPrepareObjectSql4],
            [$select5, $expectedPrepareSql5, $expectedParams5, $expectedSql5, $expectedPrepareObjectSql5],
        ];
        // phpcs:enable Generic.Files.LineLength.TooLong
    }
}
