<?php

namespace Potogan\DoctrineBundle\Collections;

use Traversable;

abstract class AbstractFilteredCollection extends AbstractInitializableCollection
{
    /**
     * Internal element list
     * 
     * @var Traversable
     */
    protected $internal;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        if (!$this->initialized) {
            $this->initialized = true;

            foreach ($this->internal as $elm) {
                if ($this->filterElement($elm)) {
                    $this->_elements[] = $elm;
                }
            }
        }
    }

    /**
     * Checks if an element should be included and returns true if it should
     * 
     * @param  mixed $element Collection item/element
     * 
     * @return boolean
     */
    abstract protected function filterElement($element);
}
