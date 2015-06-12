<?php

namespace NetRivet\WordPress;

use Illuminate\Container\Container;

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
     * Dispatch the route with resolved dependencies
     */
    public function dispatch()
    {
        return $this->resolve();
    }

    /**
     * @param Container $container
     */
    public function bind(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Resolve dependencies for the responder
     */
    public function resolve()
    {
        $responder = $this->getResponder();

        if ($this->container === null) {
            return call_user_func($responder);
        }

        $args = $this->getResolvedArguments($responder);

        return call_user_func_array($responder, $args);
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
}
