<?php

/**
 * File: RedDbCommand.php
 * User: daijianhao(toruneko@outlook.com)
 * Date: 16/3/26 02:09
 * Description:
 */
class RedDbCommand extends CDbCommand
{
    public function __construct(CDbConnection $connection, $query = null)
    {
        parent::__construct($connection, $query);
    }

    public function insertSeveral($table, $array_columns)
    {
        $sql = '';
        $params = array();
        $i = 0;
        foreach ($array_columns as $columns) {
            $names = array();
            $placeholders = array();
            foreach ($columns as $name => $value) {
                if (!$i) {
                    $names[] = $this->getConnection()->quoteColumnName($name);
                }
                if ($value instanceof CDbExpression) {
                    $placeholders[] = $value->expression;
                    foreach ($value->params as $n => $v)
                        $params[$n] = $v;
                } else {
                    $placeholders[] = ':' . $name . $i;
                    $params[':' . $name . $i] = $value;
                }
            }
            if (!$i) {
                $sql = 'INSERT INTO ' . $this->getConnection()->quoteTableName($table)
                    . ' (' . implode(', ', $names) . ') VALUES ('
                    . implode(', ', $placeholders) . ')';
            } else {
                $sql .= ',(' . implode(', ', $placeholders) . ')';
            }
            $i++;
        }
        return $this->setText($sql)->execute($params);
    }
}