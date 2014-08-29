<?php
namespace Potogan\DoctrineBundle\Annotations;

use Doctrine\ORM\Mapping\Annotation as AnnotationInterface,
	Doctrine\ORM\EntityManager,
	ReflectionProperty
;

interface QueryAssociationInterface {

	/**
	 * Initializes Annotation with additional infos
	 *
	 * @param ReflectionProperty  $property = annotated property reflection instance
	 * @param AnnotationInterface $orderBy  = Optionnal orderby annotation
	 *
	 * @return void
	 */
	public function init(ReflectionProperty $property, AnnotationInterface $orderBy = null);

	/**
	 * Creates and initializes the relation query builder
	 *
	 * @param object $entity    = entity owning relation
	 * @param EntityManager $em = Related entity manager
	 *
	 * @return void
	 */
	public function getQueryBuilder($entity, EntityManager $em);
}