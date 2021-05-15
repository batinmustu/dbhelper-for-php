<?php
namespace DBHelper;

use Exception;
use PDO;

session_start();
class DBHelper {
    private $host = '';
    private $user = '';
    private $password = '';
    private $database = '';

    private $db;
    private $where;
    private $fields;
    private $distinct;
    private $count;
    private $orderBy;
    private $limit;
    private $offset;
    private $jsonParser;
    private $values;

    public function __construct() {
        $this->resetParams();
    }

    public function connect() {
        try {
            $this->db = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->database . ';charset=utf8', $this->user, $this->password);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function disconnect() {
        $this->db = null;
    }

    public function insert($table) {
        try {
            if (is_null($this->values) OR !is_array($this->values)) return false;

            $values = $this->queryFormatter($this->values);
            $query = $this->db->query("INSERT INTO {$table} SET {$values}");
            $this->resetParams();
            unset($values, $table);

            return $query;
        } catch (Exception $e) {
            $this->resetParams();
            return false;
        }
    }
    public function get($table) {
        try {
            $fields = ($this->distinct) ? "DISTINCT {$this->fields}" : $this->fields;
            $fields = ($this->count) ? "COUNT({$fields})" : $fields;
            $where = ($this->where !== '') ? " WHERE {$this->where}" : '';
            $limit = ($this->limit !== '') ? " LIMIT {$this->limit}" : '';
            $offset = ($this->offset !== '') ? " OFFSET {$this->offset}" : '';
            $orderBy = (!is_null($this->orderBy)) ? " ORDER BY  {$this->orderBy[0]} {$this->orderBy[1]}" : '';
            $query = $this->db->query("SELECT {$fields} FROM {$table} {$where} {$orderBy} {$limit} {$offset}");

            if (!$query) {
                $this->resetParams();
                return false;
            }
            $rows = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = (!is_null($this->jsonParser) AND is_array($this->jsonParser)) ? $this->jsonToArray($row) : $row;
            }
            $this->resetParams();
            unset($fields, $where, $limit, $offset, $orderBy, $query, $table, $row);
            return $rows;
        } catch (Exception $e) {
            $this->resetParams();
            return false;
        }
    }
    public function first($table) {
        $this->limit(1);
        $this->offset('');
        return ($result = $this->get($table)) ? $result[0] : $result;
    }
    public function update($table) {
        try {
            if (is_null($this->values) AND !is_array($this->values)) return false;
            $values = (!is_null($this->jsonParser) AND is_array($this->jsonParser)) ? $this->arrayToJson($this->values) : $this->values;
            $values = $this->queryFormatter($values);

            $where = ($this->where !== '') ? " WHERE {$this->where}" : '';
            $query = $this->db->query("UPDATE $table SET $values $where");

            $this->resetParams();
            unset($values, $table, $where);

            return $query;
        } catch (Exception $e) {
            $this->resetParams();
            return false;
        }
    }
    public function delete($table) {
        try {
            $where = ($this->where !== '') ? " WHERE {$this->where}" : '';
            $query = $this->db->query("DELETE FROM $table $where");

            unset($where, $table);
            $this->resetParams();

            return $query;
        } catch (Exception $e) {
            $this->resetParams();
            return false;
        }
    }
    public function queryBuilder($query) {
        try {
            return $this->db->query($query);
        } catch (Exception $e) {
            return false;
        }
    }

    public function whereIn($where) {
        $this->whereBuilder($where, '=', 'AND');
        return $this;
    }
    public function whereOr($where) {
        $this->whereBuilder($where, '=', 'OR');
        return $this;
    }
    public function whereLike($where) {
        $this->whereBuilder($where, 'LIKE', 'AND');
        return $this;
    }
    public function whereNotIn($where) {
        $this->whereBuilder($where, '!=', 'AND');
        return $this;
    }
    public function whereNotOr($where) {
        $this->whereBuilder($where, '!=', 'OR');
        return $this;
    }
    public function whereNotLike($where) {
        $this->whereBuilder($where, 'NOT LIKE', 'AND');
        return $this;
    }
    public function fields($fields) {
        $this->fields = '';
        for ($i = 0; $i < count($fields); $i++) {
            $this->fields .= $fields[$i] . ', ';
        }
        $this->fields = trim(trim($this->fields), ",");
        return $this;
    }
    public function distinct($bool) {
        $this->distinct = $bool;
        return $this;
    }
    public function count($bool) {
        $this->count = $bool;
        return $this;
    }
    public function orderBy($field, $order = 'ASC') {
        $this->orderBy = array( $field, $order );
        return $this;
    }
    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }
    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }
    public function jsonParser($pattern) {
        $this->jsonParser = $pattern;
        return $this;
    }
    public function values($values) {
        $this->values = $values;
        return $this;
    }

    private function whereBuilder($where, $operator, $brace) {
        if ($this->where !== '') $this->where .= ' '.$brace.' ';
        for ($i = 0; $i < count($where); $i++) {
            $this->where .= array_keys($where)[$i] . " {$operator} '". addslashes($where[array_keys($where)[$i]]) ."'";
            if ($i + 1 != count($where)) $this->where .= ' '. $brace . ' ';
        }
    }
    private function queryFormatter($values) {
        $query = '';
        for ($i = 0; $i < count($values); $i++) {
            $query .= array_keys($values)[$i] . ' = "' . addslashes($values[array_keys($values)[$i]]) . '", ';
        }
        $query = trim($query, ', ');
        return $query;
    }
    private function jsonToArray($row) {
        if (!is_array($row)) return false;
        foreach ($row as $field => $data) {
            foreach ($this->jsonParser as $index => $value) {
                if (isset($row[$value]) AND !is_array($row[$value])) {
                    $row[$value] = json_decode($row[$value], true);
                }
            }
        }
        return $row;
    }
    private function arrayToJson($row) {
        if (!is_array($row)) return false;
        foreach ($row as $field => $data) {
            foreach ($this->jsonParser as $index => $value) {
                if (isset($row[$value]) AND is_array($row[$value])) {
                    $row[$value] = json_encode($row[$value]);
                }
            }
        }
        return $row;
    }
    private function resetParams() {
        $this->where = '';
        $this->fields = '*';
        $this->distinct = false;
        $this->count = false;
        $this->orderBy = null;
        $this->limit = '';
        $this->offset = '';
        $this->jsonParser = null;
        $this->values = null;
    }
}
session_destroy();