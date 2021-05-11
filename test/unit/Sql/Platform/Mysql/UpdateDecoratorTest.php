<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Sql\Platform\Mysql;

use Laminas\Db\Sql\Platform\Mysql\UpdateDecorator;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Update;
use LaminasTest\Db\TestAsset\TrustingMysqlPlatform;
use PHPUnit\Framework\TestCase;

class UpdateDecoratorTest extends TestCase
{
    /**
     * @covers
     * @dataProvider dataProvider
     */
    public function testJsonUpdate(Update $update, string $expected)
    {
        $decorator_update = new UpdateDecorator;
        $decorator_update->setSubject($update);

        $this
            ->assertEquals(
                $expected,
                $decorator_update->getSqlString(new TrustingMysqlPlatform())
            );
    }

    public function dataProvider(): array
    {
        $update = new Update;
        $update
            ->table('foo')
            ->set(
                ['data->foo.is_checked' => true]
            )
            ->where([
                'id = ?' => 1,
            ])
        ;

        $update2 = new Update;
        $update2
            ->table('foo')
            ->set(
                [
                    'data->foo.is_checked' => 1,
                    'data1'                => '5',
                ]
            )
            ->where([
                'id = ?' => 1,
            ])
        ;
        $select = new Select();
        $update3 = new Update;
        $update3->table('foo')
            ->set(
                [
                    'x'             => $select->from('foo'),
                    'data->tbl.fld' => 'test_data',
                ]
            )
            ->where([
                'id = ?' => 5,
            ])
        ;

        return [
            [$update, "UPDATE `foo` SET `data` = JSON_SET(`data`, '$.foo.is_checked', '1') WHERE id = '1'"],
            [
                $update2,
                "UPDATE `foo` SET `data` = JSON_SET(`data`, '$.foo.is_checked', '1'), `data1` = '5' WHERE id = '1'",
            ],
            [
                $update3,
                "UPDATE `foo` SET `x` = (SELECT `foo`.* FROM `foo`), "
                .
                "`data` = JSON_SET(`data`, '$.tbl.fld', 'test_data') WHERE id = '5'",
            ],
        ];
    }
}
