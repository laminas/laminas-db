<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Db\Metadata\Source;

use Laminas\Db\Adapter\Adapter;

class SqlServerMetadata extends AnsiMetadata
{
    protected function loadConstraintData($table, $schema)
    {
        if (isset($this->data['constraints'][$schema][$table])) {
            return;
        }

        $this->prepareDataHierarchy('constraints', $schema, $table);

        $isColumns = [
            ['T', 'TABLE_NAME'],
            ['TC', 'CONSTRAINT_NAME'],
            ['TC', 'CONSTRAINT_TYPE'],
            ['KCU', 'COLUMN_NAME'],
            ['CC', 'CHECK_CLAUSE'],
            ['RC', 'MATCH_OPTION'],
            ['RC', 'UPDATE_RULE'],
            ['RC', 'DELETE_RULE'],
            ['REFERENCED_TABLE_SCHEMA' => 'KCU2', 'TABLE_SCHEMA'],
            ['REFERENCED_TABLE_NAME' => 'KCU2', 'TABLE_NAME'],
            ['REFERENCED_COLUMN_NAME' => 'KCU2', 'COLUMN_NAME'],
        ];

        $p = $this->adapter->getPlatform();

        array_walk($isColumns, function (&$c) use ($p) {
            $alias = key($c);
            $c = $p->quoteIdentifierChain($c);
            if (is_string($alias)) {
                $c .= ' ' . $p->quoteIdentifier($alias);
            }
        });

        $sql = 'SELECT ' . implode(', ', $isColumns)
             . ' FROM ' . $p->quoteIdentifierChain(['INFORMATION_SCHEMA', 'TABLES']) . ' T'

             . ' INNER JOIN ' . $p->quoteIdentifierChain(['INFORMATION_SCHEMA', 'TABLE_CONSTRAINTS']) . ' TC'
             . ' ON ' . $p->quoteIdentifierChain(['T', 'TABLE_SCHEMA'])
             . '  = ' . $p->quoteIdentifierChain(['TC', 'TABLE_SCHEMA'])
             . ' AND ' . $p->quoteIdentifierChain(['T', 'TABLE_NAME'])
             . '  = ' . $p->quoteIdentifierChain(['TC', 'TABLE_NAME'])

             . ' LEFT JOIN ' . $p->quoteIdentifierChain(['INFORMATION_SCHEMA', 'KEY_COLUMN_USAGE']) . ' KCU'
             . ' ON ' . $p->quoteIdentifierChain(['TC', 'TABLE_SCHEMA'])
             . '  = ' . $p->quoteIdentifierChain(['KCU', 'TABLE_SCHEMA'])
             . ' AND ' . $p->quoteIdentifierChain(['TC', 'TABLE_NAME'])
             . '  = ' . $p->quoteIdentifierChain(['KCU', 'TABLE_NAME'])
             . ' AND ' . $p->quoteIdentifierChain(['TC', 'CONSTRAINT_NAME'])
             . '  = ' . $p->quoteIdentifierChain(['KCU', 'CONSTRAINT_NAME'])

             . ' LEFT JOIN ' . $p->quoteIdentifierChain(['INFORMATION_SCHEMA', 'CHECK_CONSTRAINTS']) . ' CC'
             . ' ON ' . $p->quoteIdentifierChain(['TC', 'CONSTRAINT_SCHEMA'])
             . '  = ' . $p->quoteIdentifierChain(['CC', 'CONSTRAINT_SCHEMA'])
             . ' AND ' . $p->quoteIdentifierChain(['TC', 'CONSTRAINT_NAME'])
             . '  = ' . $p->quoteIdentifierChain(['CC', 'CONSTRAINT_NAME'])

             . ' LEFT JOIN ' . $p->quoteIdentifierChain(['INFORMATION_SCHEMA', 'REFERENTIAL_CONSTRAINTS']) . ' RC'
             . ' ON ' . $p->quoteIdentifierChain(['TC', 'CONSTRAINT_SCHEMA'])
             . '  = ' . $p->quoteIdentifierChain(['RC', 'CONSTRAINT_SCHEMA'])
             . ' AND ' . $p->quoteIdentifierChain(['TC', 'CONSTRAINT_NAME'])
             . '  = ' . $p->quoteIdentifierChain(['RC', 'CONSTRAINT_NAME'])

             . ' LEFT JOIN ' . $p->quoteIdentifierChain(['INFORMATION_SCHEMA', 'KEY_COLUMN_USAGE']) . ' KCU2'
             . ' ON ' . $p->quoteIdentifierChain(['RC', 'UNIQUE_CONSTRAINT_SCHEMA'])
             . '  = ' . $p->quoteIdentifierChain(['KCU2', 'CONSTRAINT_SCHEMA'])
             . ' AND ' . $p->quoteIdentifierChain(['RC', 'UNIQUE_CONSTRAINT_NAME'])
             . '  = ' . $p->quoteIdentifierChain(['KCU2', 'CONSTRAINT_NAME'])
             . ' AND ' . $p->quoteIdentifierChain(['KCU', 'ORDINAL_POSITION'])
             . '  = ' . $p->quoteIdentifierChain(['KCU2', 'ORDINAL_POSITION'])

             . ' WHERE ' . $p->quoteIdentifierChain(['T', 'TABLE_NAME'])
             . ' = ' . $p->quoteTrustedValue($table)
             . ' AND ' . $p->quoteIdentifierChain(['T', 'TABLE_TYPE'])
             . ' IN (\'BASE TABLE\', \'VIEW\')';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $p->quoteIdentifierChain(['T', 'TABLE_SCHEMA'])
            . ' = ' . $p->quoteTrustedValue($schema);
        } else {
            $sql .= ' AND ' . $p->quoteIdentifierChain(['T', 'TABLE_SCHEMA'])
            . ' != \'INFORMATION_SCHEMA\'';
        }

        $sql .= ' ORDER BY CASE ' . $p->quoteIdentifierChain(['TC', 'CONSTRAINT_TYPE'])
              . " WHEN 'PRIMARY KEY' THEN 1"
              . " WHEN 'UNIQUE' THEN 2"
              . " WHEN 'FOREIGN KEY' THEN 3"
              . " WHEN 'CHECK' THEN 4"
              . " ELSE 5 END"
              . ', ' . $p->quoteIdentifierChain(['TC', 'CONSTRAINT_NAME'])
              . ', ' . $p->quoteIdentifierChain(['KCU', 'ORDINAL_POSITION']);

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $name = null;
        $constraints = [];
        $isFK = false;
        foreach ($results->toArray() as $row) {
            if ($row['CONSTRAINT_NAME'] !== $name) {
                $name = $row['CONSTRAINT_NAME'];
                $constraints[$name] = [
                    'constraint_name' => $name,
                    'constraint_type' => $row['CONSTRAINT_TYPE'],
                    'table_name'      => $row['TABLE_NAME'],
                ];
                if ('CHECK' == $row['CONSTRAINT_TYPE']) {
                    $constraints[$name]['check_clause'] = $row['CHECK_CLAUSE'];
                    continue;
                }
                $constraints[$name]['columns'] = [];
                $isFK = ('FOREIGN KEY' == $row['CONSTRAINT_TYPE']);
                if ($isFK) {
                    $constraints[$name]['referenced_table_schema'] = $row['REFERENCED_TABLE_SCHEMA'];
                    $constraints[$name]['referenced_table_name']   = $row['REFERENCED_TABLE_NAME'];
                    $constraints[$name]['referenced_columns']      = [];
                    $constraints[$name]['match_option']       = $row['MATCH_OPTION'];
                    $constraints[$name]['update_rule']        = $row['UPDATE_RULE'];
                    $constraints[$name]['delete_rule']        = $row['DELETE_RULE'];
                }
            }
            $constraints[$name]['columns'][] = $row['COLUMN_NAME'];
            if ($isFK) {
                $constraints[$name]['referenced_columns'][] = $row['REFERENCED_COLUMN_NAME'];
            }
        }

        $this->data['constraints'][$schema][$table] = $constraints;
    }

    protected function loadTriggerData($schema)
    {
        if (isset($this->data['triggers'][$schema])) {
            return;
        }

        $this->prepareDataHierarchy('triggers', $schema);

        $p = $this->adapter->getPlatform();

        $isColumns = [
//            'trigger_catalog',
//            'trigger_schema',
            'trigger_name',
            'event_manipulation',
//             'event_object_catalog',
            'event_object_schema',
            'event_object_table',
            'action_order',
            'action_condition',
            'action_statement',
            'action_orientation',
            'action_timing',
            'action_reference_old_table',
            'action_reference_new_table',
            'action_reference_old_row',
            'action_reference_new_row',
            'created',
        ];

        array_walk($isColumns, function (&$c) use ($p) {
            $c = $p->quoteIdentifier($c);
        });

        $sql = "SELECT 
                    sysobjects.name AS trigger_name,
                    USER_NAME(sysobjects.uid) AS trigger_owner,
                    s.name AS table_schema,
                    OBJECT_NAME(parent_obj) AS table_name,
                    OBJECTPROPERTY( id, 'ExecIsUpdateTrigger') AS isupdate,
                    OBJECTPROPERTY( id, 'ExecIsDeleteTrigger') AS isdelete,
                    OBJECTPROPERTY( id, 'ExecIsInsertTrigger') AS isinsert,
                    OBJECTPROPERTY( id, 'ExecIsAfterTrigger') AS isafter,
                    OBJECTPROPERTY( id, 'ExecIsInsteadOfTrigger') AS isinsteadof,
                    OBJECTPROPERTY(id, 'ExecIsTriggerDisabled') AS [disabled],
                    sysobjects.crdate AS CREATED
                FROM sysobjects
                     INNER JOIN sysusers
                                ON sysobjects.uid = sysusers.uid
            
                     INNER JOIN sys.tables t
                                ON sysobjects.parent_obj = t.object_id
            
                     INNER JOIN sys.schemas s
                                ON t.schema_id = s.schema_id
                WHERE sysobjects.type = 'TR' AND s.name= " . $p->quoteTrustedValue($schema);

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $data = [];
        foreach ($results->toArray() as $row) {
            $row = array_change_key_case($row, CASE_LOWER);
            if (null !== $row['created']) {
                $row['created'] = new \DateTime($row['created']);
            }
            $data[$row['trigger_name']] = array_merge(
                array_flip($isColumns),
                $row
            );
        }

        $this->data['triggers'][$schema] = $data;
    }
}
