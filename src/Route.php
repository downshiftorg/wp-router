<?php

namespace NetRivet\WordPress;

use NetRivet\Container\Container;

class Route
{
    /**
     * @var string
     */
    protected $slug;

    /**
     * @var callable
     */
    protected $responder;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param $slug
     * @param $command
     */
    public function __construct($slug, $responder)
    {
        $this->slug = $slug;
        $this->responder = $responder;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return callable
     */
    public function getResponder()
    {
        return $this->responder;
    }

    /**
     * @param Container $container
     */
    public function bind(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Resolve dependencies for the responder and invoke it
     *
     * @return mixed
     */
    public function resolve()
    {
        $responder = $this->getResponder();

        if ($this->container === null) {
            return $this->invoke();
        }

        if ($this->isClass($responder)) {
            $responder = $this->container->build($responder);
            return call_user_func($responder);
        }

        $args = $this->getResolvedArguments($responder);

        return $this->invoke($args);
    }

    /**
     * Get a list of arguments resolved against the container
     *
     * @param $responder
     * @return array
     */
    protected function getResolvedArguments($responder)
    {
        $reflection = new \ReflectionFunction($responder);
        $parameters = $reflection->getParameters();
        $args = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getClass()->getName();
            $args[] = $this->container->make($type);
        }
        return $args;
    }

    /**
     * Invoke the responder. Intentionally omits resolving to avoid reflecting
     * on the class more than once. Building of a class is passed off directly
     * to the container
     *
     * @param array $args
     */
    protected function invoke(array $args = array())
    {
        $responder = $this->getResponder();

        if ($this->isClass($responder)) {
            $reflection = new \ReflectionClass($responder);
            $responder = $reflection->newInstanceArgs($args);
            return call_user_func($responder);
        }

        return call_user_func_array($responder, $args);
    }

    /**
     * Check if the responder is a class
     *
     * @param mixed $responder
     * @return bool
     */
    protected function isClass($responder)
    {
        return is_string($responder) && class_exists($responder);
    }
}
