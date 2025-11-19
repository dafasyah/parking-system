<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Terjadi kesalahan. Silakan coba lagi atau hubungi admin.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
        report($e);
        return redirect()->back()->with('error','Terjadi kesalahan. Silakan coba lagi.');
    }
}
