<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {

    $middleware->redirectGuestsTo('/login');

    $middleware->web(append: [
      \App\Http\Middleware\HandleInertiaRequests::class,
      \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
    ]);

    // $middleware->statefulApi();

    $middleware->api([
      \Illuminate\Session\Middleware\StartSession::class,
      \Illuminate\View\Middleware\ShareErrorsFromSession::class,
      \Illuminate\Cookie\Middleware\EncryptCookies::class,
      \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
      \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ]);

  })
  // ->registered(function ($app) {
  //   $app->usePublicPath(path: realpath(base_path('public_html')));
  // })
  ->withExceptions(function (Exceptions $exceptions) {

    $exceptions->render(function (NotFoundHttpException $e, Request $request) {
      // return response()->view('errors.invalid-order', status: 500);
      return Inertia::render('NotFound')->toResponse($request);
    });

    $exceptions->respond(function (Response $response) {

      if ($response->getStatusCode() === 419) {

        return back()->with('notify', [
          'type' => 'danger',
          'message' => 'The page expired, please try again.',
        ]);

      }

      return $response;
    });

  })->create();
