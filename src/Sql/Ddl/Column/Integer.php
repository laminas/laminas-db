<?php

namespace Laminas\Db\Sql\Ddl\Column;

class Integer extends Column
{
    /**
     * @return array
     */
    public function getExpressionData()
    {
        $data    = parent::getExpressionData();
        $options = $this->getOptions();

        if (isset($options['length'])) {
            $data[0][1][1] .= '(' . $options['length'] . ')';
        }

        return $data;
    }
}
