<?php

namespace NetRivet\WordPress;

use Illuminate\Container\Container;

class Router
{
    /**
     * @var string $scope
     */
    protected $scope;

    /**
     * @var array
     */
    protected $routes;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param string $scope
     */
    public function __construct($scope)
    {
        $this->scope = $scope;
        $this->routes = array(
            'get' => array(),
            'post' => array()
        );
    }

    /**
     * @return string
     */
    public function getScopeParameter()
    {
        return $this->scope;
    }

    /**
     * Register a route for GET requests
     *
     * @param string $slug
     * @param callable $responder
     * @return Route
     */
    public function get($slug, $responder)
    {
        return $this->match('get', $slug, $responder);
    }

    /**
     * @param string $slug
     * @param callable $responder
     * @return Route
     */
    public function post($slug, $responder)
    {
        return $this->match('post', $slug, $responder);
    }

    /**
     * General purpose method for matching a slug to a request method
     * and command
     *
     * @param string $method
     * @param string $slug
     * @param string $command
     * @return Route
     */
    public function match($method, $slug, $responder)
    {
        $method = strtolower($method);
        $route = new Route($slug, $responder);
        $this->routes[$method][$slug] = $route;
        return $route;
    }

    /**
     * Bind the Router to a container
     *
     * @param Container $container
     */
    public function bind(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Dispatch the route if a match is found
     *
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function dispatch($method = '', $params = array())
    {
        if (empty($method)) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $method = strtolower($method);
        
        if (empty($params)) {
            $params = $this->getSuperGlobals($method);
        }

        if (! isset($params[$this->getScopeParameter()])) {
            return null;
        }

        $route = $this->getResolvedRoute($method, $params);

        return $route->dispatch();
    }

    /**
     * Get a route with the container bound if available
     *
     * @param $method
     * @param $params
     * @return Route
     */
    protected function getResolvedRoute($method, $params)
    {
        $scope = $params[$this->getScopeParameter()];

        $route = $this->routes[$method][$scope];

        if ($this->container) {
            $route->bind($this->container);
        }

        return $route;
    }

    /**
     * Return the super global array relevant to the request method
     *
     * @param string $method
     * @return array
     */
    protected function getSuperGlobals($method)
    {
        if ($method === 'get') {
            return $_GET;
        }

        return $_POST;
    }
}
