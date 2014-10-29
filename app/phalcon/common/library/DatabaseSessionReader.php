<?php
namespace Webird;

use Phalcon\Db;


/**
 * Read-only session access
 *
 */
class DatabaseSessionReader
{
    private $options;

    private $data;

    /**
     * {@inheritdoc}
     *
     * @param  array                      $options
     * @throws \Phalcon\Session\Exception
     */
    public function __construct($options = null)
    {
        if (!isset($options['db'])) {
            throw new \Exception("The parameter 'db' is required");
        }
        if (!isset($options['unique_id'])) {
            throw new \Exception("The parameter unique_id is required");
        }
        if (!isset($options['db_table'])) {
            throw new \Exception("The parameter 'db_table' is required");
        }
        if (!isset($options['db_id_col'])) {
            throw new \Exception("The parameter 'db_id_col' is required");
        }
        if (!isset($options['db_data_col'])) {
            throw new \Exception("The parameter 'db_data_col' is required");
        }
        if (!isset($options['db_time_col'])) {
            throw new \Exception("The parameter 'db_time_col' is required");
        }

        $this->options = $options;
    }


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
        $options = $this->getOptions();
        $row = $options['db']->fetchOne(
            sprintf(
                'SELECT %s FROM %s WHERE %s = ?',
                $options['db']->escapeIdentifier($options['db_data_col']),
                $options['db']->escapeIdentifier($options['db_table']),
                $options['db']->escapeIdentifier($options['db_id_col'])
            ),
            Db::FETCH_NUM,
            [$sessionId]
        );

        $this->data = (empty($row[0])) ? false : $this->unserialize_php($row[0]);
        return ($this->data !== false);
    }



    public function has($key)
    {
        if (!is_string($key)) {
            throw new \Exception('The key must be a string');
        }
        if (empty($this->data)) {
            return false;
        }

        $uniqueId = $this->getOptions()['unique_id'];
        return (array_key_exists("{$uniqueId}{$key}", $this->data));
    }


    public function get($key)
    {
        if (!$this->has($key)) {
            return false;
        }

        $uniqueId = $this->getOptions()['unique_id'];
        return $this->data["{$uniqueId}{$key}"];
    }



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
