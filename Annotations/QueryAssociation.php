<?php

namespace Potogan\DoctrineBundle\Annotations;

use Doctrine\ORM\Mapping\Annotation as AnnotationInterface;
use Doctrine\Common\Annotations\Annotation;
use ReflectionProperty;
use Doctrine\ORM\EntityManager;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class QueryAssociation extends Annotation implements QueryAssociationInterface
{
    /**
     * @var string
     */
    public $where;

    /**
     * @var array<string>
     */
    public $injectMethods = array();

    protected $property;
    protected $classname;
    protected $orderBy;

    /**
     * Returns whether multiple annotations of this type are allowed
     *
     * @return Boolean
     */
    public function allowArray()
    {
        return false;
    }

    /**
     * Initializes Annotation with additional infos
     *
     * @param ReflectionProperty  $property = annotated property reflection instance
     * @param AnnotationInterface $orderBy  = Optionnal orderby annotation
     *
     * @return void
     */
    public function init(ReflectionProperty $property, AnnotationInterface $orderBy = null)
    {
        if (is_null($this->property)) {
            $this->property = $property;
            $this->classname = $this->value;

            if (!strpos($this->classname, ':') && !class_exists($this->classname)) {
                $this->classname = $property->getDeclaringClass()->getNamespaceName()
                    . '\\' . $this->classname
                ;
            }

            $this->orderBy = $orderBy;
        }
    }

    /**
     * Creates and initializes the relation query builder
     *
     * @param object $entity    = entity owning relation
     * @param EntityManager $em = Related entity manager
     *
     * @return void
     */
    public function getQueryBuilder($entity, EntityManager $em)
    {
        $builder = $em->getRepository($this->classname)
            ->createQueryBuilder('related')
            ->where($this->where)
            ->setParameter('entity', $this->entity)
        ;

        foreach ($this->injectMethods as $key => $method) {
            $builder->setParameter($key, $entity->$method());
        }

        if (isset($this->orderBy)) {
            foreach ($this->orderBy->value as $key => $value) {
                $builder->addOrderBy($key, $value);
            }
        }

        return $builder;
    }

}
