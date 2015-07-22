<?php
use NetRivet\Container\Container;
use NetRivet\WordPress\Router;
use Rad\DependencyInterface;

describe('Router', function () {

    beforeEach(function () {
        $this->router = new Router('scoped_action');
        $_GET = [];
        $_POST = [];
        $_SERVER = [];
    });

    it("can return its scope parameter", function () {
        $scope = $this->router->getScopeParameter();

        expect($scope)->to->equal('scoped_action');
    });

    describe('->get()', function () {
        it('returns a route constructed with a slug and a responder', function () {
            $responder = function () {
                // do a thing
            };

            $route = $this->router->get('/custom-endpoint', $responder);

            expect($route)->to->be->an->instanceof('NetRivet\WordPress\Route');
            expect($route->getSlug())->to->equal('/custom-endpoint');
            expect($route->getResponder())->to->equal($responder);
        });
    });

    describe('->post()', function () {
        it('returns a route constructed with a slug and a command', function () {
            $responder = function () {
                // do a thing
            };

            $route = $this->router->post('/custom-endpoint', $responder);

            expect($route)->to->be->an->instanceof('NetRivet\WordPress\Route');
            expect($route->getSlug())->to->equal('/custom-endpoint');
            expect($route->getResponder())->to->equal($responder);
        });
    });

    describe('->dispatch()', function () {
        it('can execute a function registered via ->get()', function () {
            $executed = false;
            $this->router->get('/custom-endpoint', function () use (&$executed) {
                $executed = true;
            });

            $this->router->dispatch('GET', ['scoped_action' => '/custom-endpoint']);

            expect($executed)->to->be->true;
        });

        it('can execute a function registered via ->post()', function () {
            $executed = false;
            $this->router->post('/custom-endpoint', function () use (&$executed) {
                $executed = true;
            });

            $this->router->dispatch('POST', ['scoped_action' => '/custom-endpoint']);

            expect($executed)->to->be->true;
        });

        it('should not execute if the scope parameter is not present', function () {
            $executed = false;
            $this->router->get('/custom-endpoint', function () use (&$executed) {
                $executed = true;
            });

            $this->router->dispatch('GET', ['blah' => '/custom-endpoint']);

            expect($executed)->to->be->false;
        });

        it('should use the $_REQUEST super global to match when dispatching a route', function () {
            $_REQUEST = ['scoped_action' => '/custom-endpoint'];
            $this->router->get('/custom-endpoint', function () use (&$executed) {
                $executed = true;
            });

            $this->router->dispatch('GET');

            expect($executed)->to->be->true;
        });

        it('should forward a bound container to its routes', function () {
            $container = new Container();
            $container->bind('Rad\DependencyInterface', 'Rad\DependencyImpl');
            $this->router->bind($container);

            $dep = null;
            $this->router->get('/custom-endpoint', function (DependencyInterface $rad) use (&$dep) {
                $dep = $rad;
            });

            $this->router->dispatch('GET', ['scoped_action' => '/custom-endpoint']);

            expect($dep)->to->be->instanceof('Rad\DependencyImpl');
        });

        it('should use $_SERVER[REQUEST_METHOD] to determine parameters if no arguments provided', function () {
            $_REQUEST = ['scoped_action' => '/custom-endpoint'];
            $_SERVER = ['REQUEST_METHOD' => 'GET'];
            $this->router->get('/custom-endpoint', function () use (&$executed) {
                $executed = true;
            });

            $this->router->dispatch();

            expect($executed)->to->be->true;
        });
    });

});
