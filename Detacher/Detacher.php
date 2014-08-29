<?php
namespace Potogan\DoctrineBundle\Detacher;

use Doctrine\ORM\EntityManager;

class Detacher
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function detach($entity)
    {
        if ($this->em->contains($entity)) {
            $this->em->detach($entity);
            if ($entity instanceof DetachableEntityInterface) {
                $subs = $entity->detachChildrenEntities();

                foreach ($subs as $subentity) {
                    if (isset($subentity)) $this->detach($subentity);
                }
            }
        }
    }
}
