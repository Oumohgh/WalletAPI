<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request){
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié.',
            ], 401);
        });
        $exceptions->render(function (AccessDeniedHttpException $e, $request){
            return response()->json([
                'success' => false,
                'message' => "Vous n'êtes pas autorisé à effectuer cette action.",
            ], 403);
        });
        $exceptions->render(function (ModelNotFoundException $e, $request){
            return response()->json([
                'success' => false,
                'message' => 'Ressource introuvable',
            ], 404);
        });
        $exceptions->render(function (MethodNotAllowedHttpException $e, $request){
            return response()->json([
                'success' => false,
                'message' => 'Methode HTTP non autorisée',
            ], 405);
        });
        $exceptions->render(function (ValidationException $e, $request){
            return response()->json([
                'success' => false,
                'message' => 'Error de validation',
                'errors' => $e->errors(),
            ], 422);
        });
//        $exceptions->render(function (\Throwable $e, $request){
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Une erreur interne est survenue. Veuillez réessayer.',
//                ], 500);
//        });


    })
    ->create();
