<?php
/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2012 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  |          Nikita Vershinin <endeveit@gmail.com>                         |
  +------------------------------------------------------------------------+
*/
namespace Webird;

use Phalcon\Db;
use Phalcon\Session\Adapter;
use Phalcon\Session\AdapterInterface;
use Phalcon\Session\Exception;

/**
 * Phalcon\Session\Adapter\Database
 * Database adapter for Phalcon\Session
 */
class DatabaseSession extends Adapter implements AdapterInterface
{

    /**
     * Flag to check if session is destroyed.
     *
     * @var boolean
     */
    protected $isDestroyed = false;

    /**
     * {@inheritdoc}
     *
     * @param  array                      $options
     * @throws \Phalcon\Session\Exception
     */
    public function __construct($options = null)
    {
        if (!isset($options['db'])) {
            throw new Exception("The parameter 'db' is required");
        }
        if (!isset($options['db_table'])) {
            throw new Exception("The parameter 'db_table' is required");
        }
        if (!isset($options['db_id_col'])) {
            throw new Exception("The parameter 'db_id_col' is required");
        }
        if (!isset($options['db_data_col'])) {
            throw new Exception("The parameter 'db_data_col' is required");
        }
        if (!isset($options['db_time_col'])) {
            throw new Exception("The parameter 'db_time_col' is required");
        }

        parent::__construct($options);

        session_set_save_handler(
            [$this, 'open'],
            [$this, 'close'],
            [$this, 'read'],
            [$this, 'write'],
            [$this, 'destroy'],
            [$this, 'gc']
        );
    }

    /**
     * {@inheritdoc}
     * @return boolean
     */
    public function open()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     * @return boolean
     */
    public function close()
    {
        return false;
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

        if (!empty($row)) {
            return $row[0];
        }

        return '';
    }

    /**
     * {@inheritdoc}
     * @param  string  $sessionId
     * @param  string  $data
     * @return boolean
     */
    public function write($sessionId, $data)
    {
        if ($this->isDestroyed || empty($data)) {
            return false;
        }

        $options = $this->getOptions();
        $row = $options['db']->fetchOne(
            sprintf(
                'SELECT COUNT(*) FROM %s WHERE %s = ?',
                $options['db']->escapeIdentifier($options['db_table']),
                $options['db']->escapeIdentifier($options['db_id_col'])
            ),
            Db::FETCH_NUM,
            [$sessionId]
        );

        if (!empty($row) && intval($row[0]) > 0) {
            return $options['db']->execute(
                sprintf(
                    'UPDATE %s SET %s = ?, %s = ? WHERE %s = ?',
                    $options['db']->escapeIdentifier($options['db_table']),
                    $options['db']->escapeIdentifier($options['db_data_col']),
                    $options['db']->escapeIdentifier($options['db_time_col']),
                    $options['db']->escapeIdentifier($options['db_id_col'])
                ),
                [$data, time(), $sessionId]
            );
        } else {
            return $options['db']->execute(
                sprintf('INSERT INTO %s VALUES (?, ?, ?, 0)', $options['db']->escapeIdentifier($options['db_table'])),
                [$sessionId, $data, time()]
            );
        }
    }

    /**
     * {@inheritdoc}
     * @return boolean
     */
    public function destroy($session_id = null)
    {
        if (!$this->isStarted() || $this->isDestroyed) {
            return true;
        }
        if (is_null($session_id)) {
            $session_id = $this->getId();
        }

        error_log($session_id);

        $this->isDestroyed = true;
        $options = $this->getOptions();
        $result = $options['db']->execute(
            sprintf(
                'DELETE FROM %s WHERE %s = ?',
                $options['db']->escapeIdentifier($options['db_table']),
                $options['db']->escapeIdentifier($options['db_id_col'])
            ),
            [$session_id]
        );

        session_regenerate_id();

        return $result;
    }

    /**
     * {@inheritdoc}
     * @param  integer $maxlifetime
     * @return boolean
     */
    public function gc($maxlifetime)
    {
        $options = $this->getOptions();

        return $options['db']->execute(
            sprintf(
                'DELETE FROM %s WHERE %s + %d < ?',
                $options['db']->escapeIdentifier($options['db_table']),
                $options['db']->escapeIdentifier($options['db_time_col']),
                $maxlifetime
            ),
            [time()]
        );
    }
}
