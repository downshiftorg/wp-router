#wp-router

A simple scoped router powered by query string parameters.

Though this has use outside of WordPress (for now), it is meant for use
within the WordPress ecosystem as a means to add custom routes without
specific page scripts.

##usage

The router matches a get or post request to a responder. A responder is a function or an invokable class.

```php
use NetRivet\WordPress\Router;

$router = new Router('my_scope');

// matches ?my_scope=/myroute
$router->post('/myroute', function () {
  // do something here
});

// listen terminates via exit after route function executes
$router->listen();
```

You can also give a route definition an invokable class.

```php
$router->post('/myroute', new InvokableClass());

// or a string if you prefer
$router->post('/myroute', 'NetRivet\Responders\SomeClass');
```

##service injection

Services in route functions are resolved using a PHP 5.3 friendly version of the [Illuminate Container](https://github.com/netrivet/container)


```php
$container = new Container();
$container->bind('SomeInterface', 'SomeImplementation');
$router = new Router('my_scope');
$router->bind($container);

$router->get('/test', function (SomeInterface $service) {
  // do a thing with $service
});
```

If resolving a class, the constructor will have dependencies injected. Resolution of classes is only
valid when using a string.

##tests

Tests are written using [peridot](http://peridot-php.github.io/), and can be run like so:

```
vendor/bin/peridot
```
