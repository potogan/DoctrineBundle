<?php

namespace Potogan\DoctrineBundle\Collections;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Mapping\ClassMetadata;

class IdListCollection extends AbstractInitializableCollection
{
    /**
     * Id list
     *
     * @var array
     */
    protected $list;

    /**
     * Target entity class
     *
     * @var string
     */
    protected $class;

    /**
     * Entity manager
     *
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct($list, $class, EntityManager $entityManager)
    {
        $this->list = $list;
        $this->class = $class;
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
        $repository = $this->entityManager->getRepository($this->class);
        $metadata = $this->entityManager
            ->getMetadataFactory()
            ->getMetadataFor(ltrim($this->class, '\\'))
        ;
        $qb = $repository->createQueryBuilder('entity');

        if ($metadata->isIdentifierComposite) {
            $i = 0;

            foreach ($this->list as $id) {
                $id = $this->checkAndSortCompositeId($metadata, $id);

                $list = array();
                foreach ($id as $name => $value) {
                    $list[] = $qb->expr()->eq('entity.' . $name, ':field_' . $name . '_' . $i);

                    $qb->setParameter('field_' . $name . '_' . $i);
                }

                $qb->orWhere(call_user_func_array(array($qb->expr(), 'andX'), $list));

                $i++;
            }
        } else {
            $qb
                ->where('entity.' . $metadata->identifier[0] . ' IN (:idlist)')
                ->setParameter('idlist', $this->list)
            ;
        }

        $this->_elements = $qb
            ->getQuery()
            ->getResult()
        ;

        $this->initialized = true;
    }

    protected function checkAndSortCompositeId(ClassMetadata $class, $id)
    {
        $sortedId = array();
        
        foreach ($class->identifier as $identifier) {
            if (!isset($id[$identifier])) {
                throw ORMException::missingIdentifierField($class->name, $identifier);
            }

            $sortedId[$identifier] = $id[$identifier];
            unset($id[$identifier]);
        }

        if (count($id)) {
            throw ORMException::unrecognizedIdentifierFields($class->name, array_keys($id));
        }

        return $sortedId;
    }


}
