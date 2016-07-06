<?php

namespace Potogan\DoctrineBundle\Query;

use Doctrine\ORM\Query\SqlWalker;

/**
 * SqlWalker implementing USE INDEX from custom query hint.
 *
 * @see https://gist.github.com/arnaud-lb/2704404
 */
class UseIndexWalker extends SqlWalker
{
    const HINT_USE_INDEX = 'UseIndexWalker.UseIndex';

    /**
     * {@inheritDoc}
     */
    public function walkFromClause($fromClause)
    {
        $result = parent::walkFromClause($fromClause);

        if ($index = $this->getQuery()->getHint(self::HINT_USE_INDEX)) {
            $result = preg_replace('#(\bFROM\s*\w+\s*\w+)#', '\1 USE INDEX (' . $index . ')', $result);
        }

        return $result;
    }
}
