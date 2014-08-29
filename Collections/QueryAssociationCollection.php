<?php

namespace Potogan\DoctrineBundle\Collections;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Closure;
use ArrayIterator;
use Potogan\DoctrineBundle\Annotations\QueryAssociationInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;

class QueryAssociationCollection implements Collection
{
    /**
     * Related entity
     *
     * @var object
     */
    protected $entity;

    /**
     * Annotation defining the association
     *
     * @var QueryAssociationInterface
     */
    protected $annotation;

    /**
     * Entity manager
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Init state
     *
     * @var boolean
     */
    protected $initialized = false;

    /**
     * An array containing the entries of this collection.
     *
     * @var array|null
     */
    protected $_elements;

    public function __construct($entity, QueryAssociationInterface $annotation, EntityManager $entityManager)
    {
        $this->entity = $entity;
        $this->annotation = $annotation;
        $this->entityManager = $entityManager;
    }

    public function initialize()
    {
        if ($this->initialized) {
            return ;
        }

        $this->_elements = $this->annotation
            ->getQueryBuilder($this->entity, $this->entityManager)
            ->getQuery()
            ->getResult()
        ;

        $this->initialized = true;
    }

    /**
     * Sets the initialized flag of the collection, forcing it into that state.
     *
     * @param boolean $bool
     *
     * @return void
     */
    public function setInitialized($bool)
    {
        $this->initialized = $bool;
    }

    /**
     * Checks whether this collection has been initialized.
     *
     * @return boolean
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * Gets the PHP array representation of this collection.
     *
     * @return array The PHP array representation of this collection.
     */
    public function toArray()
    {
        $this->initialize();

        return $this->_elements;
    }

    /**
     * Sets the internal iterator to the first element in the collection and
     * returns this element.
     *
     * @return mixed
     */
    public function first()
    {
        $this->initialize();

        return reset($this->_elements);
    }

    /**
     * Sets the internal iterator to the last element in the collection and
     * returns this element.
     *
     * @return mixed
     */
    public function last()
    {
        $this->initialize();

        return end($this->_elements);
    }

    /**
     * Gets the current key/index at the current internal iterator position.
     *
     * @return mixed
     */
    public function key()
    {
        $this->initialize();

        return key($this->_elements);
    }

    /**
     * Moves the internal iterator position to the next element.
     *
     * @return mixed
     */
    public function next()
    {
        $this->initialize();

        return next($this->_elements);
    }

    /**
     * Gets the element of the collection at the current internal iterator position.
     *
     * @return mixed
     */
    public function current()
    {
        $this->initialize();

        return current($this->_elements);
    }

    /**
     * Removes an element with a specific key/index from the collection.
     *
     * @param mixed $key
     * @return mixed The removed element or NULL, if no element exists for the given key.
     */
    public function remove($key)
    {
        // Read only !
        return null;
    }

    /**
     * Removes the specified element from the collection, if it is found.
     *
     * @param mixed $element The element to remove.
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeElement($element)
    {
        // Read only !
        return false;
    }

    /**
     * ArrayAccess implementation of offsetExists()
     *
     * @see containsKey()
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * ArrayAccess implementation of offsetGet()
     *
     * @see get()
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * ArrayAccess implementation of offsetGet()
     *
     * @see add()
     * @see set()
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            return $this->add($value);
        }

        return $this->set($offset, $value);
    }

    /**
     * ArrayAccess implementation of offsetUnset()
     *
     * @see remove()
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /**
     * Checks whether the collection contains a specific key/index.
     *
     * @param mixed $key The key to check for.
     * @return boolean TRUE if the given key/index exists, FALSE otherwise.
     */
    public function containsKey($key)
    {
        $this->initialize();

        return isset($this->_elements[$key]);
    }

    /**
     * Checks whether the given element is contained in the collection.
     * Only element values are compared, not keys. The comparison of two elements
     * is strict, that means not only the value but also the type must match.
     * For objects this means reference equality.
     *
     * @param mixed $element
     * @return boolean TRUE if the given element is contained in the collection,
     *      FALSE otherwise.
     */
    public function contains($element)
    {
        $this->initialize();

        foreach ($this->_elements as $collectionElement) {
            if ($element === $collectionElement) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tests for the existance of an element that satisfies the given predicate.
     *
     * @param Closure $p The predicate.
     * @return boolean TRUE if the predicate is TRUE for at least one element, FALSE otherwise.
     */
    public function exists(Closure $p)
    {
        $this->initialize();

        foreach ($this->_elements as $key => $element) {
            if ($p($key, $element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Searches for a given element and, if found, returns the corresponding key/index
     * of that element. The comparison of two elements is strict, that means not
     * only the value but also the type must match.
     * For objects this means reference equality.
     *
     * @param mixed $element The element to search for.
     * @return mixed The key/index of the element or FALSE if the element was not found.
     */
    public function indexOf($element)
    {
        $this->initialize();

        return array_search($element, $this->_elements, true);
    }

    /**
     * Gets the element with the given key/index.
     *
     * @param mixed $key The key.
     * @return mixed The element or NULL, if no element exists for the given key.
     */
    public function get($key)
    {
        $this->initialize();

        if (isset($this->_elements[$key])) {
            return $this->_elements[$key];
        }

        return null;
    }

    /**
     * Gets all keys/indexes of the collection elements.
     *
     * @return array
     */
    public function getKeys()
    {
        $this->initialize();

        return array_keys($this->_elements);
    }

    /**
     * Gets all elements.
     *
     * @return array
     */
    public function getValues()
    {
        $this->initialize();

        return array_values($this->_elements);
    }

    /**
     * Returns the number of elements in the collection.
     *
     * Implementation of the Countable interface.
     *
     * @return integer The number of elements in the collection.
     */
    public function count()
    {
        $this->initialize();

        return count($this->_elements);
    }

    /**
     * Adds/sets an element in the collection at the index / with the specified key.
     *
     * When the collection is a Map this is like put(key,value)/add(key,value).
     * When the collection is a List this is like add(position,value).
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->initialize();

        $this->_elements[$key] = $value;
    }

    /**
     * Adds an element to the collection.
     *
     * @param mixed $value
     * @return boolean Always TRUE.
     */
    public function add($value)
    {
        // Read only !
        return true;
    }

    /**
     * Checks whether the collection is empty.
     *
     * Note: This is preferrable over count() == 0.
     *
     * @return boolean TRUE if the collection is empty, FALSE otherwise.
     */
    public function isEmpty()
    {
        $this->initialize();

        return empty($this->_elements);
    }

    /**
     * Gets an iterator for iterating over the elements in the collection.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        $this->initialize();

        return new ArrayIterator($this->_elements);
    }

    /**
     * Applies the given function to each element in the collection and returns
     * a new collection with the elements returned by the function.
     *
     * @param Closure $func
     * @return Collection
     */
    public function map(Closure $func)
    {
        $this->initialize();

        return new ArrayCollection(array_map($func, $this->_elements));
    }

    /**
     * Returns all the elements of this collection that satisfy the predicate p.
     * The order of the elements is preserved.
     *
     * @param Closure $p The predicate used for filtering.
     * @return Collection A collection with the results of the filter operation.
     */
    public function filter(Closure $p)
    {
        $this->initialize();

        return new ArrayCollection(array_filter($this->_elements, $p));
    }

    /**
     * Applies the given predicate p to all elements of this collection,
     * returning true, if the predicate yields true for all elements.
     *
     * @param Closure $p The predicate.
     * @return boolean TRUE, if the predicate yields TRUE for all elements, FALSE otherwise.
     */
    public function forAll(Closure $p)
    {
        $this->initialize();

        foreach ($this->_elements as $key => $element) {
            if ( ! $p($key, $element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Partitions this collection in two collections according to a predicate.
     * Keys are preserved in the resulting collections.
     *
     * @param Closure $p The predicate on which to partition.
     * @return array An array with two elements. The first element contains the collection
     *          of elements where the predicate returned TRUE, the second element
     *          contains the collection of elements where the predicate returned FALSE.
     */
    public function partition(Closure $p)
    {
        $coll1 = $coll2 = array();

        $this->initialize();

        foreach ($this->_elements as $key => $element) {
            if ($p($key, $element)) {
                $coll1[$key] = $element;
            } else {
                $coll2[$key] = $element;
            }
        }

        return array(new ArrayCollection($coll1), new ArrayCollection($coll2));
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . '@' . spl_object_hash($this);
    }

    /**
     * Clears the collection.
     */
    public function clear()
    {
        // Readonly !
    }

    /**
     * Extract a slice of $length elements starting at position $offset from the Collection.
     *
     * If $length is null it returns all elements from $offset to the end of the Collection.
     * Keys have to be preserved by this method. Calling this method will only return the
     * selected slice and NOT change the elements contained in the collection slice is called on.
     *
     * @param int $offset
     * @param int $length
     * @return array
     */
    public function slice($offset, $length = null)
    {
        $this->initialize();

        return array_slice($this->_elements, $offset, $length, true);
    }
}
