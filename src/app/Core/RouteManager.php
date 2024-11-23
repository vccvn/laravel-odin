<?php
namespace Odin\Core;

use Odin\Files\Filemanager;
use Illuminate\Support\Facades\Route;

/**
 * @method static \Illuminate\Routing\PendingResourceRegistration apiResource(string $name, string $controller, array $options = [])
 * @method static \Illuminate\Routing\PendingResourceRegistration resource(string $name, string $controller, array $options = [])
 * @method static \Illuminate\Routing\Route any(string $uri, array|string|callable|null $action = null)
 * @method static \Illuminate\Routing\Route|null current()
 * @method static \Illuminate\Routing\Route delete(string $uri, array|string|callable|null $action = null)
 * @method static \Illuminate\Routing\Route fallback(array|string|callable|null $action = null)
 * @method static \Illuminate\Routing\Route get(string $uri, array|string|callable|null $action = null)
 * @method static \Illuminate\Routing\Route|null getCurrentRoute()
 * @method static \Illuminate\Routing\RouteCollectionInterface getRoutes()
 * @method static \Illuminate\Routing\Route match(array|string $methods, string $uri, array|string|callable|null $action = null)
 * @method static \Illuminate\Routing\Route options(string $uri, array|string|callable|null $action = null)
 * @method static \Illuminate\Routing\Route patch(string $uri, array|string|callable|null $action = null)
 * @method static \Illuminate\Routing\Route permanentRedirect(string $uri, string $destination)
 * @method static \Illuminate\Routing\Route post(string $uri, array|string|callable|null $action = null)
 * @method static \Illuminate\Routing\Route put(string $uri, array|string|callable|null $action = null)
 * @method static \Illuminate\Routing\Route redirect(string $uri, string $destination, int $status = 302)
 * @method static \Illuminate\Routing\Route substituteBindings(\Illuminate\Support\Facades\Route $route)
 * @method static \Illuminate\Routing\Route view(string $uri, string $view, array $data = [], int|array $status = 200, array $headers = [])
 * @method static \Illuminate\Routing\RouteRegistrar as(string $value)
 * @method static \Illuminate\Routing\RouteRegistrar controller(string $controller)
 * @method static \Illuminate\Routing\RouteRegistrar domain(string $value)
 * @method static \Illuminate\Routing\RouteRegistrar middleware(array|string|null $middleware)
 * @method static \Illuminate\Routing\RouteRegistrar name(string $value)
 * @method static \Illuminate\Routing\RouteRegistrar namespace(string|null $value)
 * @method static \Illuminate\Routing\RouteRegistrar prefix(string $prefix)
 * @method static \Illuminate\Routing\RouteRegistrar scopeBindings()
 * @method static \Illuminate\Routing\RouteRegistrar where(array $where)
 * @method static \Illuminate\Routing\RouteRegistrar withoutMiddleware(array|string $middleware)
 * @method static \Illuminate\Routing\Router|\Illuminate\Routing\RouteRegistrar group(\Closure|string|array $attributes, \Closure|string $routes)
 * @method static \Illuminate\Routing\ResourceRegistrar resourceVerbs(array $verbs = [])
 * @method static string|null currentRouteAction()
 * @method static string|null currentRouteName()
 * @method static void apiResources(array $resources, array $options = [])
 * @method static void bind(string $key, string|callable $binder)
 * @method static void model(string $key, string $class, \Closure|null $callback = null)
 * @method static void pattern(string $key, string $pattern)
 * @method static void resources(array $resources, array $options = [])
 * @method static void substituteImplicitBindings(\Illuminate\Support\Facades\Route $route)
 * @method static boolean uses(...$patterns)
 * @method static boolean is(...$patterns)
 * @method static boolean has(string $name)
 * @method static mixed input(string $key, string|null $default = null)
 *
 * @see \Illuminate\Routing\Router
 */
class RouteManager{
    /**
     * Undocumented variable
     *
     * @var Filemanager
     */
    protected static $filemanager;
    protected static $routes = [
        
    ];
    
    protected static $__package = '';
    protected $__name = '';

    protected $params = [];

    public function __construct($packageName)
    {
        if($packageName){
            $this->__name = $packageName;
        }
    }

    public function __call($name, $arguments)
    {
        $this->params[$name] = $arguments;
        if(!in_array($this, static::$routes[$this->__name])){
            static::$routes[$this->__name][] = $this;
        }
    }

    /**
     * dang ký package
     *
     * @param string $packageName
     * @return RouteManager
     */
    public static function package($packageName = '')
    {
        static::$__package = $packageName;
        if(!array_key_exists($packageName, static::$routes)) static::$routes[$packageName] = [];
        $router = new static(static::$__package);
        // static::$routes[$packageName][] = $router;
        return $router;
    }

    public static function __callStatic($name, $arguments)
    {
        if(!array_key_exists(static::$__package, static::$routes)) static::$routes[static::$__package] = [];
        $router = new static(static::$__package);
        static::$routes[static::$__package][] = $router;
        return call_user_func_array([$router, $name], $arguments);
        
    }
}
