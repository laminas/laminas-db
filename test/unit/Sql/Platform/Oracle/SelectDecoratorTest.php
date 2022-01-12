<?php

namespace LaminasTest\Db\Sql\Platform\Oracle;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Platform\Oracle as OraclePlatform;
use Laminas\Db\Sql\Platform\Oracle\SelectDecorator;
use Laminas\Db\Sql\Select;
use PHPUnit\Framework\TestCase;

class SelectDecoratorTest extends TestCase
{
    /**
     * @testdox integration test: Testing SelectDecorator will use Select to produce properly Oracle
     *                            dialect prepared sql
     * @covers \Laminas\Db\Sql\Platform\SqlServer\SelectDecorator::prepareStatement
     * @covers \Laminas\Db\Sql\Platform\SqlServer\SelectDecorator::processLimitOffset
     * @dataProvider dataProvider
     * @param mixed $notUsed
     */
    public function testPrepareStatement(
        Select $select,
        string $expectedSql,
        array $expectedParams,
        $notUsed,
        int $expectedFormatParamCount
    ) {
        $driver = $this->getMockBuilder(DriverInterface::class)->getMock();
        $driver->expects($this->exactly($expectedFormatParamCount))
            ->method('formatParameterName')
            ->will($this->returnValue('?'));

        // test
        $adapter = $this->getMockBuilder(Adapter::class)
            ->setMethods()
            ->setConstructorArgs([
                $driver,
                new OraclePlatform(),
            ])
            ->getMock();

        $parameterContainer = new ParameterContainer();
        $statement          = $this->getMockBuilder(StatementInterface::class)->getMock();
        $statement->expects($this->any())
            ->method('getParameterContainer')
            ->will($this->returnValue($parameterContainer));

        $statement->expects($this->once())->method('setSql')->with($expectedSql);

        $selectDecorator = new SelectDecorator();
        $selectDecorator->setSubject($select);
        $selectDecorator->prepareStatement($adapter, $statement);

        self::assertEquals($expectedParams, $parameterContainer->getNamedArray());
    }

    /**
     * @testdox integration test: Testing SelectDecorator will use Select to produce properly Oracle
     *                            dialect sql statements
     * @covers \Laminas\Db\Sql\Platform\Oracle\SelectDecorator::getSqlString
     * @dataProvider dataProvider
     * @param mixed $ignored
     * @param mixed $alsoIgnored
     */
    public function testGetSqlString(Select $select, $ignored, $alsoIgnored, string $expectedSql)
    {
        $parameterContainer = new ParameterContainer();
        $statement          = $this->getMockBuilder(StatementInterface::class)->getMock();
        $statement->expects($this->any())
            ->method('getParameterContainer')
            ->will($this->returnValue($parameterContainer));

        $selectDecorator = new SelectDecorator();
        $selectDecorator->setSubject($select);
        self::assertEquals($expectedSql, $selectDecorator->getSqlString(new OraclePlatform()));
    }

    /**
     * Data provider for testGetSqlString
     *
     * @psalm-return array<array-key, array{
     *     0: Select,
     *     1: string,
     *     2: array<string, mixed>
     *     3: string,
     *     4: int
     * }>
     */
    public function dataProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong,WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps
        $select0 = new Select();
        $select0->from(['x' => 'foo']);
        $expectedSql0              = 'SELECT "x".* FROM "foo" "x"';
        $expectedFormatParamCount0 = 0;

        $select1a                  = new Select('test');
        $select1b                  = new Select(['a' => $select1a]);
        $select1                   = new Select(['b' => $select1b]);
        $expectedSql1              = 'SELECT "b".* FROM (SELECT "a".* FROM (SELECT "test".* FROM "test") "a") "b"';
        $expectedFormatParamCount1 = 0;

        $select2a = new Select('test');
        $select2a->limit(2);
        $select2b                  = new Select(['a' => $select2a]);
        $select2                   = new Select(['b' => $select2b]);
        $expectedSql2_1            = 'SELECT "b".* FROM (SELECT "a".* FROM (SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "test".* FROM "test" ) b WHERE rownum <= (:offset2+:limit2)) WHERE b_rownum >= (:offset2 + 1)) "a") "b"';
        $expectedSql2_2            = 'SELECT "b".* FROM (SELECT "a".* FROM (SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "test".* FROM "test" ) b WHERE rownum <= (0+2)) WHERE b_rownum >= (0 + 1)) "a") "b"';
        $expectedFormatParamCount2 = 0;
        $expectedParams2           = ['offset2' => 0, 'limit2' => 2];

        $select3a = new Select('test');
        $select3a->offset(2);
        $select3b                  = new Select(['a' => $select3a]);
        $select3                   = new Select(['b' => $select3b]);
        $expectedSql3_1            = 'SELECT "b".* FROM (SELECT "a".* FROM (SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "test".* FROM "test" ) b ) WHERE b_rownum > (:offset2)) "a") "b"';
        $expectedSql3_2            = 'SELECT "b".* FROM (SELECT "a".* FROM (SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "test".* FROM "test" ) b ) WHERE b_rownum > (2)) "a") "b"';
        $expectedFormatParamCount3 = 0;
        $expectedParams3           = ['offset2' => 2];

        $select4a = new Select('test');
        $select4a->limit(2);
        $select4a->offset(2);
        $select4b                  = new Select(['a' => $select4a]);
        $select4                   = new Select(['b' => $select4b]);
        $expectedSql4_1            = 'SELECT "b".* FROM (SELECT "a".* FROM (SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "test".* FROM "test" ) b WHERE rownum <= (:offset2+:limit2)) WHERE b_rownum >= (:offset2 + 1)) "a") "b"';
        $expectedSql4_2            = 'SELECT "b".* FROM (SELECT "a".* FROM (SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "test".* FROM "test" ) b WHERE rownum <= (2+2)) WHERE b_rownum >= (2 + 1)) "a") "b"';
        $expectedFormatParamCount4 = 0;
        $expectedParams4           = ['offset2' => 2, 'limit2' => 2];

        return [
            [$select0, $expectedSql0, [], $expectedSql0, $expectedFormatParamCount0],
            [$select1, $expectedSql1, [], $expectedSql1, $expectedFormatParamCount1],
            [$select2, $expectedSql2_1, $expectedParams2, $expectedSql2_2, $expectedFormatParamCount2],
            [$select3, $expectedSql3_1, $expectedParams3, $expectedSql3_2, $expectedFormatParamCount3],
            [$select4, $expectedSql4_1, $expectedParams4, $expectedSql4_2, $expectedFormatParamCount4],
        ];
        // phpcs:enable Generic.Files.LineLength.TooLong,WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps
    }
}
