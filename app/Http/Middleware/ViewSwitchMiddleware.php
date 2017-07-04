<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\FileViewFinder;
use Agent;

class ViewSwitchMiddleware
{
    protected $view;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
    }

    public function handle($request, Closure $next)
    {
        $ua = $request->server->get('HTTP_USER_AGENT');

        if (Agent::isMobile($ua)) {
            $app = app();
            $paths = $app['config']['view.paths'];
            array_unshift($paths, $app['config']['view.mobile_path']);

            $this->view->setFinder(new FileViewFinder($app['files'], $paths));
        } else {
          $app = app();
          $paths = $app['config']['view.paths'];
          array_unshift($paths, $app['config']['view.pc_path']);

          $this->view->setFinder(new FileViewFinder($app['files'], $paths));
        }

        return $next($request);
    }
}
