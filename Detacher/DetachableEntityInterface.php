<?php
namespace Potogan\DoctrineBundle\Detacher;

interface DetachableEntityInterface
{
    /**
     * @return array<object>
     */
    public function detachChildrenEntities();
}
