<?php

namespace Potogan\DoctrineBundle\Collections;

use Doctrine\Common\Collections\Collection;
use Potogan\DoctrineBundle\Annotations\QueryAssociationInterface;
use Doctrine\ORM\EntityManager;

class QueryAssociationCollection extends AbstractInitializableCollection implements Collection
{
    /**
     * Related entity
     *
     * @var object
     */
    protected $entity;

    /**
     * Association's definition
     *
     * @var QueryAssociationInterface
     */
    protected $definition;

    /**
     * Entity manager
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Class constructor
     * 
     * @param mixed                     $entity        Entity/subject of the QUeryAssociationCOllection
     * @param QueryAssociationInterface $definition    Association's definition (typically an annotation)
     * @param EntityManager             $entityManager Related EntityManager
     */
    public function __construct($entity, QueryAssociationInterface $definition, EntityManager $entityManager)
    {
        $this->entity = $entity;
        $this->definition = $definition;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        if ($this->initialized) {
            return ;
        }

        $this->_elements = $this->definition
            ->getQueryBuilder($this->entity, $this->entityManager)
            ->getQuery()
            ->getResult()
        ;

        $this->initialized = true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        // Read only !
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function removeElement($element)
    {
        // Read only !
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function add($value)
    {
        // Read only !
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        // Readonly !
    }
}
