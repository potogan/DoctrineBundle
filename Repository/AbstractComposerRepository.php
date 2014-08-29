<?php
namespace Potogan\DoctrineBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractComposerRepository extends EntityRepository
{
    public function __call($name, $args)
    {
        // Query composer !
        if (substr($name, 0, 8) == 'compose_') {
            $nameParts = explode('_', $name);
            array_shift($nameParts);

            $alias = 'mainEntity';
            $querybuilder = null;

            while (count($args)) {
                $arg = reset($args);

                if (is_string($arg)) {
                    $alias = $arg;
                    array_shift($args);
                } elseif ($arg instanceof QueryBuilder) {
                    $querybuilder = $arg;
                    array_shift($args);
                } else {
                    break;
                }
            }

            if (is_null($querybuilder)) {
                $querybuilder = $this->createQueryBuilder($alias);
            }

            $baseArgs = array(
                $this->createQueryBuilder($alias),
                $alias,
            );

            // each part of the name consists into a call to a repository's composer_* method
            foreach ($nameParts as $part) {
                $method = 'composer_' . $part;

                if (method_exists($this, $method)) {
                    $baseArgs[0] = call_user_func_array(
                        array($this, $method),
                        array_merge($baseArgs, array_shift($args))
                    );
                } elseif ($this->isGenericFieldWhereComposer($part)) {
                    $baseArgs[0] = call_user_func_array(
                        array($this, 'composer_genericFieldWhere'),
                        array_merge($baseArgs, array($part), array_shift($args))
                    );
                } elseif ($this->isGenericJoinComposer($part)) {
                    $baseArgs[0] = call_user_func_array(
                        array($this, 'composer_genericJoin'),
                        array_merge($baseArgs, array($part), array_shift($args))
                    );
                } else {
                    throw new \Exception('Invalid query composer part : "' . $part . '" @Todo better exception');
                }
            }

            return $baseArgs[0];
        }

        if (method_exists($this, $name . 'QueryBuilder')) {
            return call_user_func_array(array($this, $name . 'QueryBuilder'), $args)
                ->getQuery()
                ->getResult()
            ;
        } else {
            return parent::__call($name, $args);
        }
    }

    protected function isGenericFieldWhereComposer($partName)
    {
        if (substr($partName, 0, 2) == 'by') {
            $partName = substr($partName, 2);
            $partName = strtolower(substr($partName, 0, 1)) . substr($partName, 1);

            return isset($this->getClassMetaData()->columnNames[$partName])
                || isset($this->getClassMetaData()->associationMappings[$partName])
            ;
        }

        return false;
    }

    public function composer_genericFieldWhere(QueryBuilder $qb, $alias, $part, $value)
    {
        $fieldname = strtolower(substr($part, 2, 1)) . substr($part, 3);
        $paramname = $alias . '_' . $fieldname;

        if (is_array($value)) {
            $qb
                ->andWhere($alias . '.' . $fieldname .' IN (:' . $paramname . ')')
            ;
        } else {
            $qb
                ->andWhere($alias . '.' . $fieldname .' = :' . $paramname)
            ;
        }

        return $qb
            ->setParameter($paramname, $value)
        ;
    }

    protected function isGenericJoinComposer($partName)
    {
        if (substr($partName, 0, 4) == 'join') {
            $partName = substr($partName, 4);
            $partName = strtolower(substr($partName, 0, 1)) . substr($partName, 1);

            return isset($this->getClassMetaData()->associationMappings[$partName]);
        }

        return false;
    }

    public function composer_genericJoin(QueryBuilder $qb, $alias, $part)
    {
        $fieldname = strtolower(substr($part, 4, 1)) . substr($part, 5);
        $joinAlias = $alias . '_' . $fieldname;

        return $qb
            ->join($alias . '.' . $fieldname, $joinAlias)
            ->addSelect($joinAlias)
        ;
    }

    public function composer_orderBy(QueryBuilder $qb, $alias, $field, $direction)
    {
        if (!isset($this->getClassMetaData()->columnNames[$field])) {
            $field = key($this->getClassMetaData()->columnNames);
            $direction = 'ASC';
        }

        if (!in_array($direction, array('ASC', 'DESC'))) {
            $dir = 'ASC';
        }

        $qb->orderBy($alias . '.' . $field, $direction);

        return $qb;
    }

    public function composer_limit(QueryBuilder $qb, $alias, $first, $max)
    {
        return $qb
            ->setFirstResult($first)
            ->setMaxResults($max)
        ;
    }

    public function composer_execute(QueryBuilder $qb, $alias)
    {
        return $qb->getQuery()->getResult();
    }
}
