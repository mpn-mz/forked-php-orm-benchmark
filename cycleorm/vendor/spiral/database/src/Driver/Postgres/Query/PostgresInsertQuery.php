<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Database\Driver\Postgres\Query;

use Spiral\Database\Driver\DriverInterface;
use Spiral\Database\Driver\Postgres\PostgresDriver;
use Spiral\Database\Exception\BuilderException;
use Spiral\Database\Query\InsertQuery;
use Spiral\Database\Query\QueryInterface;
use Spiral\Database\Query\QueryParameters;
use Throwable;

/**
 * Postgres driver requires little bit different way to handle last insert id.
 */
class PostgresInsertQuery extends InsertQuery
{
    /** @var PostgresDriver */
    protected $driver;

    /**
     * @param DriverInterface $driver
     * @param string|null     $prefix
     * @return QueryInterface
     */
    public function withDriver(DriverInterface $driver, string $prefix = null): QueryInterface
    {
        if (!$driver instanceof PostgresDriver) {
            throw new BuilderException(
                'Postgres InsertQuery can be used only with Postgres driver'
            );
        }

        return parent::withDriver($driver, $prefix);
    }

    /**
     * @return int|string|null
     */
    public function run()
    {
        $params = new QueryParameters();
        $queryString = $this->sqlStatement($params);

        $result = $this->driver->query($queryString, $params->getParameters());

        try {
            if ($this->driver->getPrimaryKey($this->prefix, $this->table) !== null) {
                return $result->fetchColumn();
            }

            return null;
        } finally {
            $result->close();
        }
    }

    /**
     * @return array
     */
    public function getTokens(): array
    {
        $primaryKey = null;
        if ($this->driver !== null && $this->table !== null) {
            try {
                $primaryKey = $this->driver->getPrimaryKey($this->prefix, $this->table);
            } catch (Throwable $e) {
            }
        }

        return [
            'table'   => $this->table,
            'return'  => $primaryKey,
            'columns' => $this->columns,
            'values'  => $this->values
        ];
    }
}
