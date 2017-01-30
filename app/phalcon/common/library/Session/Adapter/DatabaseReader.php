<?php
namespace Webird\Session\Adapter;

use Phalcon\Db;
use Phalcon\Db\AdapterInterface as DbAdapter;

/**
 * Read-only session access
 *
 */
class DatabaseReader
{
    /**
     * @var DbAdapter
     */
    protected $connection;

    /**
     *
     */
    private $options;

    /**
     *
     */
    private $data;

    /**
     * {@inheritdoc}
     *
     * @param  array $options
     * @throws Exception
     */
    public function __construct($options = null)
    {
        if (!isset($options['db']) || !$options['db'] instanceof DbAdapter) {
            throw new Exception(
                'Parameter "db" is required and it must be an instance of Phalcon\Db\AdapterInterface'
            );
        }

        $this->connection = $options['db'];
        unset($options['db']);

        if (!isset($options['table']) || empty($options['table']) || !is_string($options['table'])) {
            throw new Exception("Parameter 'table' is required and it must be a non empty string");
        }

        $columns = ['session_id', 'data', 'created_at', 'modified_at'];
        foreach ($columns as $column) {
            $oColumn = "column_$column";
            if (!isset($options[$oColumn]) || !is_string($options[$oColumn]) || empty($options[$oColumn])) {
                $options[$oColumn] = $column;
            }
        }

        $this->options = $options;
    }

    /**
     *
     */
    protected function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     * @param  string $sessionId
     * @return string
     */
    public function read($sessionId)
    {
        $maxLifetime = (int) ini_get('session.gc_maxlifetime');

        $options = $this->getOptions();
        $row = $this->connection->fetchOne(
            sprintf(
                'SELECT %s FROM %s WHERE %s = ? AND COALESCE(%s, %s) + %d >= ?',
                $this->connection->escapeIdentifier($options['column_data']),
                $this->connection->escapeIdentifier($options['table']),
                $this->connection->escapeIdentifier($options['column_session_id']),
                $this->connection->escapeIdentifier($options['column_modified_at']),
                $this->connection->escapeIdentifier($options['column_created_at']),
                $maxLifetime
            ),
            Db::FETCH_NUM,
            [$sessionId, time()]
        );

        $this->data = (empty($row[0])) ? false : $this->unserialize_php($row[0]);
        return ($this->data !== false);
    }

    /**
     *
     */
    public function has($key)
    {
        if (!is_string($key)) {
            throw new \Exception('The key must be a string');
        }
        if (empty($this->data)) {
            return false;
        }

        return (array_key_exists($key, $this->data));
    }

    /**
     *
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            return false;
        }

        return $this->data[$key];
    }

    /**
     *
     */
    private function unserialize_php($session_data)
    {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                throw new \Exception("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }

}
