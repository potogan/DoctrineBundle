<?php

namespace Potogan\DoctrineBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Annotations\Reader;
use Potogan\DoctrineBundle\Collections\QueryAssociationCollection;

class EntityLifeCycleListener
{
    /**
     * Annotation reader
     *
     * @var Reader
     */
    protected $reader;

    /**
     * Instanciate EntityLifeCycleListener with an annotation reader
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Entity postLoad event handler method :
     *
     * @param LifecycleEventArgs $args = event arguments
     *
     * @return void
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityClass = get_class($entity);
        $em = $args->getEntityManager();

        $reflClass = $em->getClassMetaData($entityClass)->reflClass;
        $properties = $reflClass->getProperties();

        foreach ($properties as $property) {
            $annotation = $this->reader->getPropertyAnnotation(
                $property,
                'Potogan\\DoctrineBundle\\Annotations\\QueryAssociationInterface'
            );

            if ($annotation) {
                $annotation->init(
                    $property,
                    $this->reader->getPropertyAnnotation(
                        $property,
                        'Doctrine\\ORM\\Mapping\\OrderBy'
                    )
                );

                $property->setAccessible(true);
                $property->setValue($entity, new QueryAssociationCollection($entity, $annotation, $em));
                $property->setAccessible($property->isPublic());
            }
        }
    }

    /**
     * Entity postPersist event handler method :
     *
     * @param LifecycleEventArgs $args = event arguments
     *
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->postLoad($args);
    }
}
