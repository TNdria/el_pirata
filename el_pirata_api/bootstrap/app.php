<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Ajout dans un groupe si besoin :
        $middleware->appendToGroup('api', \Illuminate\Http\Middleware\HandleCors::class);

        // Enregistrement des middlewares nommés (routeMiddleware)
        $middleware->alias([
            'last.activity' => App\Http\Middleware\UpdateLastActivity::class,
            'force.https' => App\Http\Middleware\ForceHttps::class,
            'check.user.blocked' => App\Http\Middleware\CheckUserBlocked::class,
            'verify.captcha' => App\Http\Middleware\VerifyCaptcha::class,
            'verify.math.captcha' => App\Http\Middleware\VerifyMathCaptcha::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
