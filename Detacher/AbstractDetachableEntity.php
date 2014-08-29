<?php

namespace Potogan\DoctrineBundle\Detacher;

use Doctrine\Common\Collections\Collection;

abstract class AbstractDetachableEntity implements DetachableEntityInterface
{
    protected function detachByProperties(array $properties)
    {
        $res = array();

        foreach ($properties as $name) {
            $val = $this->$name;

            if ($val instanceof Collection) {
                $res = array_merge($res, $val->getValues());
            } else {
                $res[] = $val;
            }

            $this->$name = null;
        }

        return $res;
    }
}
