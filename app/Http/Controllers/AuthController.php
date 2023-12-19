<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Services\LoginService;
use App\Services\RegistrationService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthController extends Controller
{
    private readonly RegistrationService $registrationService;

    public function __construct()
    {
        $this->registrationService = new RegistrationService();
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = (new LoginService())->processLogin($request);

            return response()->json(['data' => $user], HttpCode::HTTP_OK);
        } catch (ModelNotFoundException $modelNotFoundException) {
            $message = 'Wrong email address';
        } catch (AuthenticationException $authenticationException) {
            $message = $authenticationException->getMessage();
        }

        return response()->json(['message' => $message], HttpCode::HTTP_UNAUTHORIZED);
    }

    public function registration(RegistrationRequest $request): JsonResponse
    {
        $user = $this->registrationService->processRegistration($request);

        return response()->json([
            'message' => 'User successfully registered.',
            'data'    => $user,
        ], HttpCode::HTTP_CREATED);
    }

    public function confirmEmail(Request $request): JsonResponse
    {
        try {
            $user = $this->registrationService->processConfirmUrlRequest($request);

            return response()->json([
                'message' => 'Email verified successfully',
                'data'    => $user,
            ], HttpCode::HTTP_OK);
        } catch (NotFoundHttpException $notFoundHttpException) {
            return response()->json([
                'message' => $notFoundHttpException->getMessage(),
            ], HttpCode::HTTP_NOT_FOUND);
        }
    }
}
