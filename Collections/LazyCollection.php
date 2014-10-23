<?php

namespace Potogan\DoctrineBundle\Collections;

use Doctrine\ORM\Query;

/**
 * LazyCollection : this collection is an abstraction of a Query, and will execute this query only
 *     once, and only when needed
 */
class LazyCollection extends AbstractInitializableCollection
{
    /**
     * Query
     *
     * @var Query
     */
    protected $query;

    /**
     * Class constructor
     * 
     * @param Query $query The query which will be executed if the collection is acceded
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        if ($this->initialized) {
            return ;
        }

        $this->_elements = $this->query->getResult();

        $this->initialized = true;
    }

}
