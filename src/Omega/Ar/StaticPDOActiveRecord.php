<?php

namespace Omega\Ar;

use Omega\DateTime\Instant;

abstract class StaticPDOActiveRecord implements \ArrayAccess
{
    // Table name. Override it
    const TABLE       = null;
    // Primary key name. You can override it
    const PRIMARY_KEY = 'id';

    /**
     * Default, broadcast PDO object
     *
     * \PDO
     */
    private static $_pdoMain = null;
    /**
     * List of pdo objects per table
     *
     * @var \PDO
     */
    private static $_pdoConnectionsPool = array();
    /**
     * Pool of buffered objects
     *
     * @var StaticPDOActiveRecord[]
     */
    private static $_idBuffer = array();

    /**
     * Active record content
     *
     * @var array
     */
    private $_data = array();

    /**
     * Active record changes
     *
     * @var array
     */
    private $_changes = array();

    /**
     * Set main PDO connection
     *
     * @param \PDO $pdo
     * @return void
     */
    public static function addBroadcastPdoConnection(\PDO $pdo)
    {
        self::$_pdoMain = $pdo;
    }

    /**
     * Set PDO per table
     *
     * @param \PDO $pdo
     * @param string|string[] $tableOrTables
     * @return void
     */
    public static function addPdoConnection(\PDO $pdo, $tableOrTables)
    {
        foreach ((array) $tableOrTables as $table) {
            self::$_pdoConnectionsPool[$table] = $pdo;
        }
    }

    /**
     * Returns PDO connection for table
     *
     * @param string $table
     * @return \PDO
     * @throws \LogicException
     */
    public static function getPdoConnection($table = null)
    {
        if ($table === null) {
            $table = static::TABLE;
        }

        if (isset(self::$_pdoConnectionsPool[$table])) {
            return self::$_pdoConnectionsPool[$table];
        }

        if (self::$_pdoMain !== null) {
            return self::$_pdoMain;
        }

        throw new \LogicException("Cant find PDO connection for {$table}");
    }

    /**
     * Finds all records for SQL
     * Replaces :: to table name
     *
     * @param string $sql
     * @param array  $params
     *
     * @return static[]
     */
    public static function findAllBySql($sql, $params = array())
    {
        // Replacing table placeholder
        $sql = str_replace('::', '`' . static::TABLE . '`', $sql);

        $stmt = static::getPdoConnection()->prepare($sql);
        if (is_array($params) && count($params) > 0) {
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();
        $pool = array();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT)) {
                $pool[] = new static($row);
            }
        }

        return $pool;
    }

    /**
     * Finds entries by attributes
     *
     * @param array $attributes
     * @return static[]
     */
    public static function findAllByAttributes($attributes)
    {
        $sql = 'SELECT * FROM :: WHERE 1 ';
        $params = array();
        foreach ($attributes as $key => $value) {
            $sql .= "`{$key}` = :{$key}";
            $params[':' . $key] = $value;
        }

        return static::findAllBySql($sql, $params);
    }

    /**
     * Finds entry by ID
     *
     * @param int $id
     * @return static Or null
     */
    public static function findById($id)
    {
        $results = static::findAllBySql(
            'SELECT * FROM :: WHERE `'
            . static::PRIMARY_KEY
            . '` = :id LIMIT 1',
            array(':id', $id)
        );
        if (count($results) != 1) {
            return null;
        } else {
            return $results[0];
        }
    }

    /**
     * Finds entry by ID and bufferizes it for script execution time
     *
     * @param int $id
     * @return null|static
     */
    public static function findByIdBuffered($id)
    {
        $key = get_called_class() . '-' . $id;
        if (!array_key_exists($key, self::$_idBuffer)) {
            self::$_idBuffer[$key] = self::findById($id);
        }

        return self::$_idBuffer[$key];
    }

    /**
     * Protected constructor
     *
     * @param array $initial
     */
    protected function __construct($initial = array())
    {
        $this->_data = $initial;
    }

    /**
     * Magic getter
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Magic setter
     *
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this) . ' #' . $this->getId();
    }

    /**
     * Returns integer value
     *
     * @param string $offset
     * @return int
     */
    protected function _getInt($offset)
    {
        return (int) $this[$offset];
    }

    /**
     * Returns float value
     *
     * @param string $offset
     * @return float
     */
    protected function _getFloat($offset)
    {
        return (float) $this[$offset];
    }

    /**
     * Returns instant
     *
     * @param string $offset
     * @return Instant
     */
    protected function _getInstantFromMysql($offset)
    {
        return new Instant($this[$offset]);
    }

    /**
     * @return bool
     */
    public function isNewRecord()
    {
        return isset($this->_data[static::PRIMARY_KEY]);
    }

    /**
     * @return string
     */
    public function getCollection()
    {
        return static::TABLE;
    }

    /**
     * Returns ID of entry
     *
     * @return int
     */
    public function getId()
    {
        return $this->_data[static::PRIMARY_KEY];
    }

    /**
     * Returns true if active record contains value
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_data);
    }

    /**
     * Returns value
     *
     * @param string $offset
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function offsetGet($offset)
    {
        if (!isset($this[$offset])) {
            throw new \InvalidArgumentException(
                "$offset not found in " . get_class($this)
            );
        }

        return $this->_data[$offset];
    }

    /**
     * Sets value
     *
     * @param string $offset
     * @param mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->_changes[$offset] = $value;
        $this->_data[$offset]    = $value;
    }

    /**
     * Unset value
     *
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_changes[$offset]);
        unset($this->_data[$offset]);
    }

    /**
     * Saves changes for active record
     *
     * @return void
     */
    public function save()
    {
        if (count($this->_changes) !== 0) {
            if ($this->isNewRecord()) {
                // Inserting
                $table  = $this->getCollection();
                $keys   = implode(', ', array_map(function($x) {return "`$x`";}, array_keys($this->_changes)));
                $values = array_values($this->_changes);
                $ph     = implode(', ', array_fill(0, count($values), '?'));
                $stmt = $this->getPdoConnection($table)->prepare(
                    "INSERT INTO {$table} ($keys) VALUES ({$ph})"
                );
                $stmt->execute($values);
                $this[self::PRIMARY_KEY] = $this->getPdoConnection($table)->lastInsertId();
            } else {
                // UPDATING
                $table  = $this->getCollection();
                $keys   = array_map(function($x) {return "`$x`";}, array_keys($this->_changes));
                $values = array_values($this->_changes);
                $ph     = array_map(function($x){return ':' . $x;}, $keys);
                $total  = array();
                // JOINING
                for ($i = 0; $i < count($keys); $i++) {
                    $total[] = $keys[$i] . ' = ' . $ph[$i];
                }
                $total  = implode(', ', $total);
                $pk     = self::PRIMARY_KEY;
                $stmt = $this->getPdoConnection($table)->prepare(
                    "UPDATE {$table} SET {$total} WHERE `{$pk}` = :{$pk} LIMIT 1"
                );
                $replacements = array_combine($ph, $values);
                $replacements[$pk] = $this->getId();
                foreach ($replacements as $k => $v) {
                    $stmt->bindValue($k, $v);
                }
                $stmt->execute();
            }
        }

        $this->_changes = array();
    }


    /**
     * Returns true if current object has same class, as $another,
     * and their IDs are same
     *
     * @param StaticPDOActiveRecord $another
     * @return bool
     */
    public function equals(StaticPDOActiveRecord $another)
    {
        return get_class($this) == get_class($another)
            && $this->getId() == $another->getId();
    }
}