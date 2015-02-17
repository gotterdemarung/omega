<?php

namespace Omega\Db;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class AbstractPdoRepository
 *
 * Simple class to be used as base class for repositories
 *
 * @package Omega\Db
 */
abstract class AbstractPdoRepository
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Creates new repository instance
     *
     * @param \PDO                 $pdo
     * @param LoggerInterface|null $logger
     */
    public function __construct(\PDO $pdo, LoggerInterface $logger = null)
    {
        $this->pdo = $pdo;
        $this->logger = $logger === null ? new NullLogger() : $logger;
    }

    /**
     * Prepares statement by sql and placeholders
     * Then executes and returns it
     *
     * @param string $sql
     * @param array  $placeholders
     * @return \PDOStatement
     * @throws \InvalidArgumentException If sql is empty or not a string
     * @throws \PDOException             On database-specific errors
     */
    protected function exec($sql, array $placeholders)
    {
        // Validating sql
        if (!\is_string($sql)) {
            $this->logger->debug('Invalid argument provided as $sql');
            throw new \InvalidArgumentException('Invalid argument provided as $sql');
        }
        if (empty($sql)) {
            $this->logger->debug('Empty SQL provided');
            throw new \InvalidArgumentException('Empty SQL provided');
        }

        $statement = $this->pdo->prepare($sql, []);
        $statement->execute($placeholders);

        return $statement;
    }

    /**
     * Prepares and executes multi line result sql query
     *
     * @param string                   $sql
     * @param array                    $placeholders
     * @param HashMapperInterface|null $mapper
     *
     * @return array|object[] Linear array of associative arrays or list of objects if mapper provided
     * @throws \InvalidArgumentException If sql is empty or not a string
     * @throws \PDOException             On database-specific errors
     */
    protected function findMulti($sql, array $placeholders, HashMapperInterface $mapper = null)
    {
        $result = $this->exec($sql, $placeholders)->fetchAll(\PDO::FETCH_ASSOC);
        $this->logger->debug('Exec assoc multi returns ' . count($result));
        if ($mapper === null) {
            return $result;
        }

        // Mapping associative arrays into objects
        foreach ($result as &$row) {
            $row = $mapper->fromHashMap($row);
        }
        return $result;
    }

    /**
     * Prepares and executes single line result sql query
     * WARNING: this method DOES NOT add LIMIT 1, you MUST do that
     * by yourself
     *
     * @param string                   $sql
     * @param array                    $placeholders
     * @param HashMapperInterface|null $mapper
     *
     * @return array|object|null Returns associative array, object (if mapper provided) or null, if no data found
     * @throws \InvalidArgumentException If sql is empty or not a string
     * @throws \PDOException             On database-specific errors
     * @throws \LogicException           If database returns more
     *                                   that one result
     */
    protected function findOne($sql, array $placeholders, HashMapperInterface $mapper = null)
    {
        $result = $this->findMulti($sql, $placeholders, $mapper);
        if (\count($result) == 0) {
            return null;
        } elseif (\count($result) == 1) {
            return $result[0];
        } else {
            $message = 'Repository error. _execAssocOne should return single entry'
                . ' but ' . count($result) . ' entries received for ' . $sql
                . '. Please read doc, maybe you forgot to add LIMIT to query.';
            $this->logger->warning($message);
            throw new \LogicException($message);
        }
    }

    
}