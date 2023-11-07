<?php

namespace App\Http\Middleware;

use Closure;

use Firebase\JWT\JWT;

class CheckUserToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->header('x-api-key')) {
            return response()->json([
                "message" => "Token Not Exist",
                "data" => $request->all(),
                "code" => "E_EMPTY_TOKEN"
            ],403);
        }

        try {
            $user = std_get([
                "select" => ["MA_USRACC_ID"],
                "table_name" => "MA_USRACC",
                "where" => [
                    [
                        "field_name" => "MA_USRACC_JWT_TOKEN",
                        "operator" => "=",
                        "value" => $request->header('x-api-key')
                    ]
                ],
                "first_row" => true
            ]);
            if ($user == NULL) {
                return response()->json([
                    "message" => "Token Not Exist",
                    "data" => $request->all(),
                    "code" => "E_INVALID_TOKEN"
                ],403);
            }
            $decoded = JWT::decode($request->header('x-api-key'), config('secret.php_jwt_key'), array('HS256'));
            $request->merge(["user_data" => $decoded]);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Token Not Exist",
                "data" => $request->all(),
                "code" => "E_INVALID_TOKEN"
            ],403);
        }
        return $next($request);
    }
}
