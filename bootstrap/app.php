<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Pengguna yang sudah login dan membuka halaman tamu (login/register)
        // diarahkan ke halaman utamanya masing-masing.
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();

            return ($user && $user->isAdmin())
                ? route('admin.dashboard')
                : route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /*
         * Semua error ditampilkan lewat halaman custom RentVentures,
         * tidak ada tampilan error bawaan Laravel (termasuk saat APP_DEBUG=true).
         * Error tetap dicatat di storage/logs/laravel.log.
         */
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($e instanceof ValidationException
                || $e instanceof AuthenticationException
                || $e instanceof HttpResponseException) {
                return null; // ditangani Laravel: kembali ke form + pesan / arahkan ke login
            }

            if ($request->expectsJson()) {
                return null;
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $view = view()->exists("errors.$status") ? "errors.$status" : 'errors.generic';

            return response()->view($view, ['status' => $status], $status);
        });
    })->create();
