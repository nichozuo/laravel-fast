<?php

namespace Nichozuo\LaravelCommon\Middleware;


use Closure;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JsonResponseMiddleware
{
    /**
     * @intro 处理json响应的中间件
     * @param $request
     * @param Closure $next
     * @return JsonResponse|mixed|BinaryFileResponse
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $base = [
            'code' => 0,
            'message' => 'ok',
        ];

        // 处理不被code和message包裹的响应
        $responseDoNotWrap = config('common.responseDoNotWrap', []);
        if (count($responseDoNotWrap)) {
            $pathInfo = $request->getPathInfo();
            if (in_array($pathInfo, $responseDoNotWrap)) {
                return $response;
            }
        }

        // 处理json响应
        if ($response instanceof JsonResponse) {

            $data = $response->getData();
            $type = gettype($data);

            if ($type == 'object') {
                // 如果是exception
                if (property_exists($data, 'code') && property_exists($data, 'message') && $data->code !== 0)
                    return $response->setData($data);

                // 处理一些与分页一起的其他数据
                if (property_exists($data, 'additions')) {
                    $base['additions'] = $data->additions;
                    unset($data->additions);
                }

                // 处理标准分页
                if (property_exists($data, 'data') && property_exists($data, 'current_page')) {
                    $base['data'] = $data->data;
                    $base['meta'] = [
                        'total' => $data->total ?? 0,
                        'per_page' => (int)$data->per_page ?? 0,
                        'current_page' => $data->current_page ?? 0,
                        'last_page' => $data->last_page ?? 0
                    ];
                } else {
                    $base['data'] = $data;
                }
            } else {
                if ($data != '' && $data != null) {
                    $base['data'] = $data;
                }
            }
            return $response->setData($base);
        } elseif ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return $response;
        } elseif ($response->getContent() == "") {
            return response()->json($base, 200);
        } else {
            return $response;
        }
    }
}