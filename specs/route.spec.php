<?php
use NetRivet\Container\Container;
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

        $route->resolve();

        expect($injected)->to->be->instanceof('Rad\DependencyImpl');
    });

    it('should be able to resolve dependencies of a class responder', function () {
        $route = new Route('slug', 'Rad\Responder');
        $route->bind($this->container);

        $dep = $route->resolve();

        expect($dep)->to->be->an->instanceof('Rad\DependencyImpl');
    });

    it('should be able to resolve dependencies registered as a factory', function () {
        $container = new Container();
        $container->bind('Rad\DependencyInterface', function () {
           return new \Rad\DependencyImpl();
        });
        $route = new Route('slug', 'Rad\Responder');
        $route->bind($container);

        $dep = $route->resolve();

        expect($dep)->to->be->an->instanceof('Rad\DependencyImpl');
    });

    it('should be able to resolve a route if container not bound', function () {
        $executed = false;
        $route = new Route('slug', function (DependencyInterface $ignoreMe) use (&$executed) {
            $executed = true;
        });

        $route->resolve();

        expect($executed)->to->be->true;
    });
});
