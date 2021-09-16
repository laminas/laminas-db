<?php

namespace LaminasTest\Db\Sql;

use Laminas\Db\Adapter;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Sql;
use Laminas\Db\Sql\AbstractSql;
use Laminas\Db\Sql\Ddl\Column\Column;
use Laminas\Db\Sql\Ddl\CreateTable;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Platform\PlatformDecoratorInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Update;
use LaminasTest\Db\TestAsset;
use PHPUnit\Framework\TestCase;

use function array_merge;
use function is_array;
use function is_string;

/**
 * @method Select select(null|string $table)
 * @method Update update(null|string $table)
 * @method Delete delete(null|string $table)
 * @method Insert insert(null|string $table)
 * @method CreateTable createTable(null|string $table)
 * @method Column createColumn(null|string $name)
 */
class SqlFunctionalTest extends TestCase
{
    /**
     * @psalm-return array<string, array{
     *     sqlObject: AbstractSql,
     *     expected: array{
     *         sql92: {
     *             string: string,
     *             prepare: string,
     *             parameters: array<string, mixed>
     *         },
     *         MySql: {
     *             string: string,
     *             prepare: string,
     *             parameters: array<string, mixed>
     *         },
     *         Oracle: {
     *             string: string,
     *             prepare: string,
     *             parameters: array<string, mixed>
     *         },
     *         SqlServer: {
     *             string: string,
     *             prepare: string,
     *             parameters: array<string, mixed>
     *         }
     *     }
     * }>
     */
    protected function dataProviderCommonProcessMethods(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'Select::processOffset()'      => [
                'sqlObject' => $this->select('foo')->offset(10),
                'expected'  => [
                    'sql92'     => [
                        'string'     => 'SELECT "foo".* FROM "foo" OFFSET \'10\'',
                        'prepare'    => 'SELECT "foo".* FROM "foo" OFFSET ?',
                        'parameters' => ['offset' => 10],
                    ],
                    'MySql'     => [
                        'string'     => 'SELECT `foo`.* FROM `foo` LIMIT 18446744073709551615 OFFSET 10',
                        'prepare'    => 'SELECT `foo`.* FROM `foo` LIMIT 18446744073709551615 OFFSET ?',
                        'parameters' => ['offset' => 10],
                    ],
                    'Oracle'    => [
                        'string'     => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b ) WHERE b_rownum > (10)',
                        'prepare'    => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b ) WHERE b_rownum > (:offset)',
                        'parameters' => ['offset' => 10],
                    ],
                    'SqlServer' => [
                        'string'     => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__LAMINAS_ROW_NUMBER] FROM [foo] ) AS [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__LAMINAS_ROW_NUMBER] BETWEEN 10+1 AND 0+10',
                        'prepare'    => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__LAMINAS_ROW_NUMBER] FROM [foo] ) AS [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__LAMINAS_ROW_NUMBER] BETWEEN ?+1 AND ?+?',
                        'parameters' => ['offset' => 10, 'limit' => null, 'offsetForSum' => 10],
                    ],
                ],
            ],
            'Select::processLimit()'       => [
                'sqlObject' => $this->select('foo')->limit(10),
                'expected'  => [
                    'sql92'     => [
                        'string'     => 'SELECT "foo".* FROM "foo" LIMIT \'10\'',
                        'prepare'    => 'SELECT "foo".* FROM "foo" LIMIT ?',
                        'parameters' => ['limit' => 10],
                    ],
                    'MySql'     => [
                        'string'     => 'SELECT `foo`.* FROM `foo` LIMIT 10',
                        'prepare'    => 'SELECT `foo`.* FROM `foo` LIMIT ?',
                        'parameters' => ['limit' => 10],
                    ],
                    'Oracle'    => [
                        'string'     => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b WHERE rownum <= (0+10)) WHERE b_rownum >= (0 + 1)',
                        'prepare'    => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b WHERE rownum <= (:offset+:limit)) WHERE b_rownum >= (:offset + 1)',
                        'parameters' => ['offset' => 0, 'limit' => 10],
                    ],
                    'SqlServer' => [
                        'string'     => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__LAMINAS_ROW_NUMBER] FROM [foo] ) AS [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__LAMINAS_ROW_NUMBER] BETWEEN 0+1 AND 10+0',
                        'prepare'    => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__LAMINAS_ROW_NUMBER] FROM [foo] ) AS [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__LAMINAS_ROW_NUMBER] BETWEEN ?+1 AND ?+?',
                        'parameters' => ['offset' => null, 'limit' => 10, 'offsetForSum' => null],
                    ],
                ],
            ],
            'Select::processLimitOffset()' => [
                'sqlObject' => $this->select('foo')->limit(10)->offset(5),
                'expected'  => [
                    'sql92'     => [
                        'string'     => 'SELECT "foo".* FROM "foo" LIMIT \'10\' OFFSET \'5\'',
                        'prepare'    => 'SELECT "foo".* FROM "foo" LIMIT ? OFFSET ?',
                        'parameters' => ['limit' => 10, 'offset' => 5],
                    ],
                    'MySql'     => [
                        'string'     => 'SELECT `foo`.* FROM `foo` LIMIT 10 OFFSET 5',
                        'prepare'    => 'SELECT `foo`.* FROM `foo` LIMIT ? OFFSET ?',
                        'parameters' => ['limit' => 10, 'offset' => 5],
                    ],
                    'Oracle'    => [
                        'string'     => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b WHERE rownum <= (5+10)) WHERE b_rownum >= (5 + 1)',
                        'prepare'    => 'SELECT * FROM (SELECT b.*, rownum b_rownum FROM ( SELECT "foo".* FROM "foo" ) b WHERE rownum <= (:offset+:limit)) WHERE b_rownum >= (:offset + 1)',
                        'parameters' => ['offset' => 5, 'limit' => 10],
                    ],
                    'SqlServer' => [
                        'string'     => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__LAMINAS_ROW_NUMBER] FROM [foo] ) AS [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__LAMINAS_ROW_NUMBER] BETWEEN 5+1 AND 10+5',
                        'prepare'    => 'SELECT * FROM ( SELECT [foo].*, ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS [__LAMINAS_ROW_NUMBER] FROM [foo] ) AS [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION] WHERE [LAMINAS_SQL_SERVER_LIMIT_OFFSET_EMULATION].[__LAMINAS_ROW_NUMBER] BETWEEN ?+1 AND ?+?',
                        'parameters' => ['offset' => 5, 'limit' => 10, 'offsetForSum' => 5],
                    ],
                ],
            ],
            // Github issue https://github.com/zendframework/zend-db/issues/98
            'Select::processJoinNoJoinedColumns()' => [
                'sqlObject' => $this->select('my_table')
                                    ->join(
                                        'joined_table2',
                                        'my_table.id = joined_table2.id',
                                        $columns = []
                                    )
                                    ->join(
                                        'joined_table3',
                                        'my_table.id = joined_table3.id',
                                        [Select::SQL_STAR]
                                    )
                                    ->columns([
                                        'my_table_column',
                                        'aliased_column' => new Expression('NOW()'),
                                    ]),
                'expected'  => [
                    'sql92'     => [
                        'string' => 'SELECT "my_table"."my_table_column" AS "my_table_column", NOW() AS "aliased_column", "joined_table3".* FROM "my_table" INNER JOIN "joined_table2" ON "my_table"."id" = "joined_table2"."id" INNER JOIN "joined_table3" ON "my_table"."id" = "joined_table3"."id"',
                    ],
                    'MySql'     => [
                        'string' => 'SELECT `my_table`.`my_table_column` AS `my_table_column`, NOW() AS `aliased_column`, `joined_table3`.* FROM `my_table` INNER JOIN `joined_table2` ON `my_table`.`id` = `joined_table2`.`id` INNER JOIN `joined_table3` ON `my_table`.`id` = `joined_table3`.`id`',
                    ],
                    'Oracle'    => [
                        'string' => 'SELECT "my_table"."my_table_column" AS "my_table_column", NOW() AS "aliased_column", "joined_table3".* FROM "my_table" INNER JOIN "joined_table2" ON "my_table"."id" = "joined_table2"."id" INNER JOIN "joined_table3" ON "my_table"."id" = "joined_table3"."id"',
                    ],
                    'SqlServer' => [
                        'string' => 'SELECT [my_table].[my_table_column] AS [my_table_column], NOW() AS [aliased_column], [joined_table3].* FROM [my_table] INNER JOIN [joined_table2] ON [my_table].[id] = [joined_table2].[id] INNER JOIN [joined_table3] ON [my_table].[id] = [joined_table3].[id]',
                    ],
                ],
            ],
            'Select::processJoin()'                => [
                'sqlObject' => $this->select('a')
                                    ->join(['b' => $this->select('c')->where(['cc' => 10])], 'd=e')->where(['x' => 20]),
                'expected'  => [
                    'sql92'     => [
                        'string'     => 'SELECT "a".*, "b".* FROM "a" INNER JOIN (SELECT "c".* FROM "c" WHERE "cc" = \'10\') AS "b" ON "d"="e" WHERE "x" = \'20\'',
                        'prepare'    => 'SELECT "a".*, "b".* FROM "a" INNER JOIN (SELECT "c".* FROM "c" WHERE "cc" = ?) AS "b" ON "d"="e" WHERE "x" = ?',
                        'parameters' => ['subselect1where1' => 10, 'where1' => 20],
                    ],
                    'MySql'     => [
                        'string'     => 'SELECT `a`.*, `b`.* FROM `a` INNER JOIN (SELECT `c`.* FROM `c` WHERE `cc` = \'10\') AS `b` ON `d`=`e` WHERE `x` = \'20\'',
                        'prepare'    => 'SELECT `a`.*, `b`.* FROM `a` INNER JOIN (SELECT `c`.* FROM `c` WHERE `cc` = ?) AS `b` ON `d`=`e` WHERE `x` = ?',
                        'parameters' => ['subselect2where1' => 10, 'where2' => 20],
                    ],
                    'Oracle'    => [
                        'string'     => 'SELECT "a".*, "b".* FROM "a" INNER JOIN (SELECT "c".* FROM "c" WHERE "cc" = \'10\') "b" ON "d"="e" WHERE "x" = \'20\'',
                        'prepare'    => 'SELECT "a".*, "b".* FROM "a" INNER JOIN (SELECT "c".* FROM "c" WHERE "cc" = ?) "b" ON "d"="e" WHERE "x" = ?',
                        'parameters' => ['subselect2where1' => 10, 'where2' => 20],
                    ],
                    'SqlServer' => [
                        'string'     => 'SELECT [a].*, [b].* FROM [a] INNER JOIN (SELECT [c].* FROM [c] WHERE [cc] = \'10\') AS [b] ON [d]=[e] WHERE [x] = \'20\'',
                        'prepare'    => 'SELECT [a].*, [b].* FROM [a] INNER JOIN (SELECT [c].* FROM [c] WHERE [cc] = ?) AS [b] ON [d]=[e] WHERE [x] = ?',
                        'parameters' => ['subselect2where1' => 10, 'where2' => 20],
                    ],
                ],
            ],
            'Ddl::CreateTable::processColumns()'   => [
                'sqlObject' => $this->createTable('foo')
                                    ->addColumn($this->createColumn('col1')
                                        ->setOption('identity', true)
                                        ->setOption('comment', 'Comment1'))
                                    ->addColumn($this->createColumn('col2')
                                        ->setOption('identity', true)
                                        ->setOption('comment', 'Comment2')),
                'expected'  => [
                    'sql92'     => "CREATE TABLE \"foo\" ( \n    \"col1\" INTEGER NOT NULL,\n    \"col2\" INTEGER NOT NULL \n)",
                    'MySql'     => "CREATE TABLE `foo` ( \n    `col1` INTEGER NOT NULL AUTO_INCREMENT COMMENT 'Comment1',\n    `col2` INTEGER NOT NULL AUTO_INCREMENT COMMENT 'Comment2' \n)",
                    'Oracle'    => "CREATE TABLE \"foo\" ( \n    \"col1\" INTEGER NOT NULL,\n    \"col2\" INTEGER NOT NULL \n)",
                    'SqlServer' => "CREATE TABLE [foo] ( \n    [col1] INTEGER NOT NULL,\n    [col2] INTEGER NOT NULL \n)",
                ],
            ],
            'Ddl::CreateTable::processTable()'     => [
                'sqlObject' => $this->createTable('foo')->setTemporary(true),
                'expected'  => [
                    'sql92'     => "CREATE TEMPORARY TABLE \"foo\" ( \n)",
                    'MySql'     => "CREATE TEMPORARY TABLE `foo` ( \n)",
                    'Oracle'    => "CREATE TEMPORARY TABLE \"foo\" ( \n)",
                    'SqlServer' => "CREATE TABLE [#foo] ( \n)",
                ],
            ],
            'Select::processSubSelect()'           => [
                'sqlObject' => $this
                    ->select([
                        'a' => $this
                            ->select([
                                'b' => $this->select('c')->where(['cc' => 'CC']),
                            ])
                            ->where(['bb' => 'BB']),
                    ])
                    ->where(['aa' => 'AA']),
                'expected'  => [
                    'sql92'     => [
                        'string'     => 'SELECT "a".* FROM (SELECT "b".* FROM (SELECT "c".* FROM "c" WHERE "cc" = \'CC\') AS "b" WHERE "bb" = \'BB\') AS "a" WHERE "aa" = \'AA\'',
                        'prepare'    => 'SELECT "a".* FROM (SELECT "b".* FROM (SELECT "c".* FROM "c" WHERE "cc" = ?) AS "b" WHERE "bb" = ?) AS "a" WHERE "aa" = ?',
                        'parameters' => ['subselect2where1' => 'CC', 'subselect1where1' => 'BB', 'where1' => 'AA'],
                    ],
                    'MySql'     => [
                        'string'     => 'SELECT `a`.* FROM (SELECT `b`.* FROM (SELECT `c`.* FROM `c` WHERE `cc` = \'CC\') AS `b` WHERE `bb` = \'BB\') AS `a` WHERE `aa` = \'AA\'',
                        'prepare'    => 'SELECT `a`.* FROM (SELECT `b`.* FROM (SELECT `c`.* FROM `c` WHERE `cc` = ?) AS `b` WHERE `bb` = ?) AS `a` WHERE `aa` = ?',
                        'parameters' => ['subselect4where1' => 'CC', 'subselect3where1' => 'BB', 'where2' => 'AA'],
                    ],
                    'Oracle'    => [
                        'string'     => 'SELECT "a".* FROM (SELECT "b".* FROM (SELECT "c".* FROM "c" WHERE "cc" = \'CC\') "b" WHERE "bb" = \'BB\') "a" WHERE "aa" = \'AA\'',
                        'prepare'    => 'SELECT "a".* FROM (SELECT "b".* FROM (SELECT "c".* FROM "c" WHERE "cc" = ?) "b" WHERE "bb" = ?) "a" WHERE "aa" = ?',
                        'parameters' => ['subselect4where1' => 'CC', 'subselect3where1' => 'BB', 'where2' => 'AA'],
                    ],
                    'SqlServer' => [
                        'string'     => 'SELECT [a].* FROM (SELECT [b].* FROM (SELECT [c].* FROM [c] WHERE [cc] = \'CC\') AS [b] WHERE [bb] = \'BB\') AS [a] WHERE [aa] = \'AA\'',
                        'prepare'    => 'SELECT [a].* FROM (SELECT [b].* FROM (SELECT [c].* FROM [c] WHERE [cc] = ?) AS [b] WHERE [bb] = ?) AS [a] WHERE [aa] = ?',
                        'parameters' => ['subselect4where1' => 'CC', 'subselect3where1' => 'BB', 'where2' => 'AA'],
                    ],
                ],
            ],
            'Delete::processSubSelect()'           => [
                'sqlObject' => $this->delete('foo')->where(['x' => $this->select('foo')->where(['x' => 'y'])]),
                'expected'  => [
                    'sql92'     => [
                        'string'     => 'DELETE FROM "foo" WHERE "x" = (SELECT "foo".* FROM "foo" WHERE "x" = \'y\')',
                        'prepare'    => 'DELETE FROM "foo" WHERE "x" = (SELECT "foo".* FROM "foo" WHERE "x" = ?)',
                        'parameters' => ['subselect1where1' => 'y'],
                    ],
                    'MySql'     => [
                        'string'     => 'DELETE FROM `foo` WHERE `x` = (SELECT `foo`.* FROM `foo` WHERE `x` = \'y\')',
                        'prepare'    => 'DELETE FROM `foo` WHERE `x` = (SELECT `foo`.* FROM `foo` WHERE `x` = ?)',
                        'parameters' => ['subselect2where1' => 'y'],
                    ],
                    'Oracle'    => [
                        'string'     => 'DELETE FROM "foo" WHERE "x" = (SELECT "foo".* FROM "foo" WHERE "x" = \'y\')',
                        'prepare'    => 'DELETE FROM "foo" WHERE "x" = (SELECT "foo".* FROM "foo" WHERE "x" = ?)',
                        'parameters' => ['subselect3where1' => 'y'],
                    ],
                    'SqlServer' => [
                        'string'     => 'DELETE FROM [foo] WHERE [x] = (SELECT [foo].* FROM [foo] WHERE [x] = \'y\')',
                        'prepare'    => 'DELETE FROM [foo] WHERE [x] = (SELECT [foo].* FROM [foo] WHERE [x] = ?)',
                        'parameters' => ['subselect4where1' => 'y'],
                    ],
                ],
            ],
            'Update::processSubSelect()'           => [
                'sqlObject' => $this->update('foo')->set(['x' => $this->select('foo')]),
                'expected'  => [
                    'sql92'     => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo")',
                    'MySql'     => 'UPDATE `foo` SET `x` = (SELECT `foo`.* FROM `foo`)',
                    'Oracle'    => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo")',
                    'SqlServer' => 'UPDATE [foo] SET [x] = (SELECT [foo].* FROM [foo])',
                ],
            ],
            'Insert::processSubSelect()'           => [
                'sqlObject' => $this->insert('foo')->select($this->select('foo')->where(['x' => 'y'])),
                'expected'  => [
                    'sql92'     => [
                        'string'     => 'INSERT INTO "foo"  SELECT "foo".* FROM "foo" WHERE "x" = \'y\'',
                        'prepare'    => 'INSERT INTO "foo"  SELECT "foo".* FROM "foo" WHERE "x" = ?',
                        'parameters' => ['subselect1where1' => 'y'],
                    ],
                    'MySql'     => [
                        'string'     => 'INSERT INTO `foo`  SELECT `foo`.* FROM `foo` WHERE `x` = \'y\'',
                        'prepare'    => 'INSERT INTO `foo`  SELECT `foo`.* FROM `foo` WHERE `x` = ?',
                        'parameters' => ['subselect2where1' => 'y'],
                    ],
                    'Oracle'    => [
                        'string'     => 'INSERT INTO "foo"  SELECT "foo".* FROM "foo" WHERE "x" = \'y\'',
                        'prepare'    => 'INSERT INTO "foo"  SELECT "foo".* FROM "foo" WHERE "x" = ?',
                        'parameters' => ['subselect3where1' => 'y'],
                    ],
                    'SqlServer' => [
                        'string'     => 'INSERT INTO [foo]  SELECT [foo].* FROM [foo] WHERE [x] = \'y\'',
                        'prepare'    => 'INSERT INTO [foo]  SELECT [foo].* FROM [foo] WHERE [x] = ?',
                        'parameters' => ['subselect4where1' => 'y'],
                    ],
                ],
            ],
            'Update::processExpression()'          => [
                'sqlObject' => $this->update('foo')->set(
                    ['x' => new Sql\Expression('?', [$this->select('foo')->where(['x' => 'y'])])]
                ),
                'expected'  => [
                    'sql92'     => [
                        'string'     => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo" WHERE "x" = \'y\')',
                        'prepare'    => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo" WHERE "x" = ?)',
                        'parameters' => ['subselect1where1' => 'y'],
                    ],
                    'MySql'     => [
                        'string'     => 'UPDATE `foo` SET `x` = (SELECT `foo`.* FROM `foo` WHERE `x` = \'y\')',
                        'prepare'    => 'UPDATE `foo` SET `x` = (SELECT `foo`.* FROM `foo` WHERE `x` = ?)',
                        'parameters' => ['subselect2where1' => 'y'],
                    ],
                    'Oracle'    => [
                        'string'     => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo" WHERE "x" = \'y\')',
                        'prepare'    => 'UPDATE "foo" SET "x" = (SELECT "foo".* FROM "foo" WHERE "x" = ?)',
                        'parameters' => ['subselect3where1' => 'y'],
                    ],
                    'SqlServer' => [
                        'string'     => 'UPDATE [foo] SET [x] = (SELECT [foo].* FROM [foo] WHERE [x] = \'y\')',
                        'prepare'    => 'UPDATE [foo] SET [x] = (SELECT [foo].* FROM [foo] WHERE [x] = ?)',
                        'parameters' => ['subselect4where1' => 'y'],
                    ],
                ],
            ],
            'Update::processJoins()'               => [
                'sqlObject' => $this->update('foo')->set(['x' => 'y'])->where(['xx' => 'yy'])->join(
                    'bar',
                    'bar.barId = foo.barId'
                ),
                'expected'  => [
                    'sql92'     => [
                        'string' => 'UPDATE "foo" INNER JOIN "bar" ON "bar"."barId" = "foo"."barId" SET "x" = \'y\' WHERE "xx" = \'yy\'',
                    ],
                    'MySql'     => [
                        'string' => 'UPDATE `foo` INNER JOIN `bar` ON `bar`.`barId` = `foo`.`barId` SET `x` = \'y\' WHERE `xx` = \'yy\'',
                    ],
                    'Oracle'    => [
                        'string' => 'UPDATE "foo" INNER JOIN "bar" ON "bar"."barId" = "foo"."barId" SET "x" = \'y\' WHERE "xx" = \'yy\'',
                    ],
                    'SqlServer' => [
                        'string' => 'UPDATE [foo] INNER JOIN [bar] ON [bar].[barId] = [foo].[barId] SET [x] = \'y\' WHERE [xx] = \'yy\'',
                    ],
                ],
            ],
        ];
        // phpcs:enable Generic.Files.LineLength.TooLong
    }

    /**
     * @psalm-return array<string, array{
     *     sqlObject: AbstractSql,
     *     expected: array{
     *         sql92: array{
     *             decorators: array<class-string, PlatformDecoratorInterface>,
     *             string: string
     *         },
     *         MySql: array{
     *             decorators: array<class-string, PlatformDecoratorInterface>,
     *             string: string
     *         },
     *         Oracle: array{
     *             decorators: array<class-string, PlatformDecoratorInterface>,
     *             string: string
     *         },
     *         SqlServer: array{
     *             decorators: array<class-string, PlatformDecoratorInterface>,
     *             string: string
     *         }
     *     }
     * }>
     */
    protected function dataProviderDecorators(): array
    {
        return [
            'RootDecorators::Select' => [
                'sqlObject' => $this->select('foo')->where(['x' => $this->select('bar')]),
                'expected'  => [
                    'sql92'     => [
                        'decorators' => [
                            Select::class => new TestAsset\SelectDecorator(),
                        ],
                        'string'     => 'SELECT "foo".* FROM "foo" WHERE "x" = (SELECT "bar".* FROM "bar")',
                    ],
                    'MySql'     => [
                        'decorators' => [
                            Select::class => new TestAsset\SelectDecorator(),
                        ],
                        'string'     => 'SELECT `foo`.* FROM `foo` WHERE `x` = (SELECT `bar`.* FROM `bar`)',
                    ],
                    'Oracle'    => [
                        'decorators' => [
                            Select::class => new TestAsset\SelectDecorator(),
                        ],
                        'string'     => 'SELECT "foo".* FROM "foo" WHERE "x" = (SELECT "bar".* FROM "bar")',
                    ],
                    'SqlServer' => [
                        'decorators' => [
                            Select::class => new TestAsset\SelectDecorator(),
                        ],
                        'string'     => 'SELECT [foo].* FROM [foo] WHERE [x] = (SELECT [bar].* FROM [bar])',
                    ],
                ],
            ],
            // phpcs:disable Generic.Files.LineLength.TooLong
            /* TODO - should be implemented
            'RootDecorators::Insert' => array(
                'sqlObject' => $this->insert('foo')->select($this->select()),
                'expected'  => array(
                    'sql92'     => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Insert' => new TestAsset\InsertDecorator, // Decorator for root sqlObject
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_Sql92=}')
                        ),
                        'string' => 'INSERT INTO "foo"  {=SELECT_Sql92=}',
                    ),
                    'MySql'     => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Insert' => new TestAsset\InsertDecorator, // Decorator for root sqlObject
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_MySql=}')
                        ),
                        'string' => 'INSERT INTO `foo`  {=SELECT_MySql=}',
                    ),
                    'Oracle'    => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Insert' => new TestAsset\InsertDecorator, // Decorator for root sqlObject
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\Oracle\SelectDecorator', '{=SELECT_Oracle=}')
                        ),
                        'string' => 'INSERT INTO "foo"  {=SELECT_Oracle=}',
                    ),
                    'SqlServer' => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Insert' => new TestAsset\InsertDecorator, // Decorator for root sqlObject
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\SqlServer\SelectDecorator', '{=SELECT_SqlServer=}')
                        ),
                        'string' => 'INSERT INTO [foo]  {=SELECT_SqlServer=}',
                    ),
                ),
            ),
            'RootDecorators::Delete' => array(
                'sqlObject' => $this->delete('foo')->where(array('x'=>$this->select('foo'))),
                'expected'  => array(
                    'sql92'     => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Delete' => new TestAsset\DeleteDecorator,
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_Sql92=}')
                        ),
                        'string' => 'DELETE FROM "foo" WHERE "x" = ({=SELECT_Sql92=})',
                    ),
                    'MySql'     => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Delete' => new TestAsset\DeleteDecorator,
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_MySql=}')
                        ),
                        'string' => 'DELETE FROM `foo` WHERE `x` = ({=SELECT_MySql=})',
                    ),
                    'Oracle'    => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Delete' => new TestAsset\DeleteDecorator,
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\Oracle\SelectDecorator', '{=SELECT_Oracle=}')
                        ),
                        'string' => 'DELETE FROM "foo" WHERE "x" = ({=SELECT_Oracle=})',
                    ),
                    'SqlServer' => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Delete' => new TestAsset\DeleteDecorator,
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\SqlServer\SelectDecorator', '{=SELECT_SqlServer=}')
                        ),
                        'string' => 'DELETE FROM [foo] WHERE [x] = ({=SELECT_SqlServer=})',
                    ),
                ),
            ),
            'RootDecorators::Update' => array(
                'sqlObject' => $this->update('foo')->where(array('x'=>$this->select('foo'))),
                'expected'  => array(
                    'sql92'     => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Update' => new TestAsset\UpdateDecorator,
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_Sql92=}')
                        ),
                        'string' => 'UPDATE "foo" SET  WHERE "x" = ({=SELECT_Sql92=})',
                    ),
                    'MySql'     => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Update' => new TestAsset\UpdateDecorator,
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_MySql=}')
                        ),
                        'string' => 'UPDATE `foo` SET  WHERE `x` = ({=SELECT_MySql=})',
                    ),
                    'Oracle'    => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Update' => new TestAsset\UpdateDecorator,
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\Oracle\SelectDecorator', '{=SELECT_Oracle=}')
                        ),
                        'string' => 'UPDATE "foo" SET  WHERE "x" = ({=SELECT_Oracle=})',
                    ),
                    'SqlServer' => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Update' => new TestAsset\UpdateDecorator,
                            'Laminas\Db\Sql\Select' => array('Laminas\Db\Sql\Platform\SqlServer\SelectDecorator', '{=SELECT_SqlServer=}')
                        ),
                        'string' => 'UPDATE [foo] SET  WHERE [x] = ({=SELECT_SqlServer=})',
                    ),
                ),
            ),
            'DecorableExpression()' => array(
                'sqlObject' => $this->update('foo')->where(array('x'=>new Sql\Expression('?', array($this->select('foo'))))),
                'expected'  => array(
                    'sql92'     => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Expression' => new TestAsset\DecorableExpression,
                            'Laminas\Db\Sql\Select'     => array('Laminas\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_Sql92=}')
                        ),
                        'string'     => 'UPDATE "foo" SET  WHERE "x" = {decorate-({=SELECT_Sql92=})-decorate}',
                    ),
                    'MySql'     => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Expression' => new TestAsset\DecorableExpression,
                            'Laminas\Db\Sql\Select'     => array('Laminas\Db\Sql\Platform\Mysql\SelectDecorator', '{=SELECT_MySql=}')
                        ),
                        'string'     => 'UPDATE `foo` SET  WHERE `x` = {decorate-({=SELECT_MySql=})-decorate}',
                    ),
                    'Oracle'    => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Expression' => new TestAsset\DecorableExpression,
                            'Laminas\Db\Sql\Select'     => array('Laminas\Db\Sql\Platform\Oracle\SelectDecorator', '{=SELECT_Oracle=}')
                        ),
                        'string'     => 'UPDATE "foo" SET  WHERE "x" = {decorate-({=SELECT_Oracle=})-decorate}',
                    ),
                    'SqlServer' => array(
                        'decorators' => array(
                            'Laminas\Db\Sql\Expression' => new TestAsset\DecorableExpression,
                            'Laminas\Db\Sql\Select'     => array('Laminas\Db\Sql\Platform\SqlServer\SelectDecorator', '{=SELECT_SqlServer=}')
                        ),
                        'string'     => 'UPDATE [foo] SET  WHERE [x] = {decorate-({=SELECT_SqlServer=})-decorate}',
                    ),
                ),
            ),*/
            // phpcs:enable Generic.Files.LineLength.TooLong
        ];
    }

    /**
     * @psalm-return array<string, array{
     *     sqlObject: AbstractSql,
     *     platform: string,
     *     expected: array{
     *         sql92: array{
     *             string: string,
     *             prepare: string,
     *             parameters: array<string, mixed>
     *         },
     *         MySql: array{
     *             string: string,
     *             prepare: string,
     *             parameters: array<string, mixed>
     *         },
     *         Oracle: array{
     *             string: string,
     *             prepare: string,
     *             parameters: array<string, mixed>
     *         },
     *         SqlServer: array{
     *             string: string,
     *             prepare: string,
     *             parameters: array<string, mixed>
     *         },
     *     }
     * }>
     */
    public function dataProvider(): array
    {
        $data = array_merge(
            $this->dataProviderCommonProcessMethods(),
            $this->dataProviderDecorators()
        );

        $res = [];
        foreach ($data as $index => $test) {
            foreach ($test['expected'] as $platform => $expected) {
                $res[$index . '->' . $platform] = [
                    'sqlObject' => $test['sqlObject'],
                    'platform'  => $platform,
                    'expected'  => $expected,
                ];
            }
        }
        return $res;
    }

    /**
     * @param type $sqlObject
     * @param type $platform
     * @param type $expected
     * @dataProvider dataProvider
     */
    public function test($sqlObject, $platform, $expected)
    {
        $sql = new Sql\Sql($this->resolveAdapter($platform));

        if (is_array($expected) && isset($expected['decorators'])) {
            foreach ($expected['decorators'] as $type => $decorator) {
                $sql->getSqlPlatform()->setTypeDecorator($type, $this->resolveDecorator($decorator));
            }
        }

        $expectedString = is_string($expected) ? $expected : ($expected['string'] ?? null);
        if ($expectedString) {
            $actual = $sql->getSqlStringForSqlObject($sqlObject);
            self::assertEquals($expectedString, $actual, "getSqlString()");
        }
        if (is_array($expected) && isset($expected['prepare'])) {
            $actual = $sql->prepareStatementForSqlObject($sqlObject);
            self::assertEquals($expected['prepare'], $actual->getSql(), "prepareStatement()");
            if (isset($expected['parameters'])) {
                $actual = $actual->getParameterContainer()->getNamedArray();
                self::assertSame($expected['parameters'], $actual, "parameterContainer()");
            }
        }
    }

    /**
     * @param array|Sql\Platform\PlatformDecoratorInterface $decorator
     * @psalm-param array{0: class-string, 1: * string}|Sql\Platform\PlatformDecoratorInterface $decorator
     * @return null|PlatformDecoratorInterface
     * @psalm-return null|PlatformDecoratorInterface|PlatformDecoratorInterface&MockObject
     */
    protected function resolveDecorator($decorator)
    {
        if (is_array($decorator)) {
            $decoratorMock = $this->getMockBuilder($decorator[0])
                ->setMethods(['buildSqlString'])
                ->setConstructorArgs([null])
                ->getMock();
            $decoratorMock->expects($this->any())->method('buildSqlString')->will($this->returnValue($decorator[1]));
            return $decoratorMock;
        }

        if ($decorator instanceof Sql\Platform\PlatformDecoratorInterface) {
            return $decorator;
        }

        return null;
    }

    protected function resolveAdapter(string $platform): Adapter\Adapter
    {
        switch ($platform) {
            case 'sql92':
                $platform = new TestAsset\TrustingSql92Platform();
                break;
            case 'MySql':
                $platform = new TestAsset\TrustingMysqlPlatform();
                break;
            case 'Oracle':
                $platform = new TestAsset\TrustingOraclePlatform();
                break;
            case 'SqlServer':
                $platform = new TestAsset\TrustingSqlServerPlatform();
                break;
            default:
                $platform = null;
        }

        $mockDriver = $this->getMockBuilder(DriverInterface::class)->getMock();
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnCallback(function () {
            return new Adapter\StatementContainer();
        }));

        return new Adapter\Adapter($mockDriver, $platform);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return AbstractSql
     */
    public function __call($name, $arguments)
    {
        $arg0 = $arguments[0] ?? null;
        switch ($name) {
            case 'select':
                return new Sql\Select($arg0);
            case 'delete':
                return new Sql\Delete($arg0);
            case 'update':
                return new Sql\Update($arg0);
            case 'insert':
                return new Sql\Insert($arg0);
            case 'createTable':
                return new Sql\Ddl\CreateTable($arg0);
            case 'createColumn':
                return new Sql\Ddl\Column\Column($arg0);
        }
    }
}
