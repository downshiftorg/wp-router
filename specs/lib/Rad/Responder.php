<?php

namespace Rad;

class Responder
{
    protected $dep;

    public function __construct(DependencyInterface $dep)
    {
        $this->dep = $dep;
    }

    public function __invoke()
    {
        return $this->getDep();
    }

    public function getDep()
    {
        return $this->dep;
    }
}
