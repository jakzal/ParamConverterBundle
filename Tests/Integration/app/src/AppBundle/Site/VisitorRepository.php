<?php

namespace AppBundle\Site;

class VisitorRepository
{
    const NOT_SUPPORTED_NAME = 'foo';

    public function findByName($name)
    {
        if (self::NOT_SUPPORTED_NAME === $name) {
            return;
        }

        return new Visitor($name);
    }
}
