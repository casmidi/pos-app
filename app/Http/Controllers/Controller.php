<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

abstract class Controller
{
    protected function isGridAsyncRequest(Request $request): bool
    {
        return $request->headers->get('X-Premium-Grid') === '1';
    }

    protected function isApiRequest(Request $request): bool
    {
        if ($this->isGridAsyncRequest($request)) {
            return false;
        }

        return $request->expectsJson()
            || $request->wantsJson()
            || $request->is('api/*')
            || $request->routeIs('api.*');
    }

    protected function respond(Request $request, string $view, array $viewData = [], mixed $jsonData = null, int $status = 200): View|JsonResponse|Response
    {
        if ($this->isGridAsyncRequest($request)) {
            /** @var \Illuminate\View\View $viewInstance */
            $viewInstance = view($view, $viewData);
            $sections = $viewInstance->renderSections();
            $html = $sections['content'] ?? '';

            return response($html, 200)->header('Content-Type', 'text/html; charset=utf-8');
        }

        if ($this->isApiRequest($request)) {
            return response()->json($jsonData ?? $viewData, $status);
        }

        return view($view, $viewData);
    }

    protected function respondAfterMutation(
        Request $request,
        string $route,
        string $flashMessage,
        mixed $jsonData,
        int $status = 200,
    ): RedirectResponse|JsonResponse {
        if ($this->isApiRequest($request)) {
            return response()->json($jsonData, $status);
        }

        return redirect()->route($route)->with('success', $flashMessage);
    }
}
