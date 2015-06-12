<?php
use Illuminate\Container\Container;
use NetRivet\WordPress\Route;
use Rad\DependencyInterface;

describe('Route', function () {

    beforeEach(function () {
        $this->container = new Container();
        $this->container->bind('Rad\DependencyInterface', 'Rad\DependencyImpl');
    });

    it('should be able to resolve dependencies of a responder', function () {
        $injected = null;
        $route = new Route('slug', function (DependencyInterface $rad) use (&$injected) {
            $injected = $rad;
        });
        $route->bind($this->container);

        $route->dispatch();

        expect($injected)->to->be->instanceof('Rad\DependencyImpl');
    });

    it('should be able to dispatch a route if container not bound', function () {
        $executed = false;
        $route = new Route('slug', function (DependencyInterface $ignoreMe) use (&$executed) {
            $executed = true;
        });

        $route->dispatch();

        expect($executed)->to->be->true;
    });
});
