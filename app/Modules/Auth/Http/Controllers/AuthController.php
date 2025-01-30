<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Http\Requests\EmailVerifyRequest;
use App\Modules\Auth\Http\Requests\ForgotRequest;
use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Http\Requests\NewPasswordRequest;
use App\Modules\Auth\Http\Requests\OauthRequest;
use App\Modules\Auth\Http\Requests\RegistrationRequest;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Core\Exceptions\AuthException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Authorization.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/LoginRequest")
     *             )
     *      ),
     *     @OA\Response(
     *          response=200, description="Successfull login.",
     *          @OA\JsonContent(ref="#/components/schemas/UserToken")
     *      ),
     *     @OA\Response(response=403, description="Wrong data."),
     *     @OA\Response(response=404, description="User not found."),
     *     @OA\Response(response=422, description="Validation error.")
     * )
     *
     * @param LoginRequest $request
     * @param AuthService $service
     * @return string
     * @throws \App\Modules\Core\Exceptions\AuthException
     */
    public function login(LoginRequest $request, AuthService $service)
    {
        return $service->login($request->validated());
    }

    /**
     * @OA\Post(
     *     path="/auth/registration",
     *     summary="User registration.",
     *     description="Remark: When creating a user, a default group is generated for its links.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/RegisterRequest")
     *             )
     *      ),
     *     @OA\Response(
     *          response=200, description="Successfull registration.",
     *          @OA\JsonContent(ref="#/components/schemas/UserToken")
     *      ),
     *     @OA\Response(response=422, description="Validation error.")
     * )
     *
     * @param RegistrationRequest $request
     * @param AuthService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function registration(RegistrationRequest $request, AuthService $service)
    {
        return $service->registration($request->validated());
    }

    /**
     * @OA\Get(
     *     path="/auth/logout",
     *     summary="User logout.",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *          response=200, description="Successfull logout.",
     *          @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated.")
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Successful logout.',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/email/resend",
     *     summary="Resending email verification.",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *          response=200, description="Email sent.",
     *          @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated.")
     * )
     * @param AuthService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Modules\Core\Exceptions\EmailException
     */
    public function resend(AuthService $service)
    {
        return $service->resend(auth()->user());
    }

    /**
     * @OA\Post(
     *     path="/email/verify",
     *     summary="Email verification.",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *          description="The token that comes to the user in the email.",
     *          in="query",
     *          name="token",
     *          required=true,
     *          example="ZXlKcGRpSTZJbGR5VGpoU2RXRllSM0UwYkc1MGJqRnJiRkoyYVhjOVBTSXNJblpoYkhWbElqb2llVEp5WjNkbWJEZGtSMUZQSzJaUU5UaHhORmRuUTFoWGVtNXRXV3RPYUdKdlQzTklaVE12UkVGeVp6MGlMQ0p0WVdNaU9pSXdNRFl6TjJGa05qbGtaalkyT1RVd016ZGtPVFExTTJGa01UY3lNVFZsT1RKbU5HTmhZalkyTVdRMU0yRmxabUZtWkROak5XRTVZekF3WVRGbU9UUmtJaXdpZEdGbklqb2lJbjA9"
     *      ),
     *      @OA\Response(response=200, description="Email verified."),
     *     @OA\Response(response=403, description="Link expired."),
     *     @OA\Response(response=404, description="User not found.")
     * )
     * @param Request $request
     * @param AuthService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Modules\Core\Exceptions\EmailException
     */
    public function emailVerify(EmailVerifyRequest $request, AuthService $service)
    {
        return $service->emailVerify($request->validated());
    }

    /**
     * @OA\Post(
     *     path="/auth/forgot-password",
     *     summary="Changing the password.",
     *     description="Sending a password recovery email.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/ForgotRequest")
     *             )
     *      ),
     *     @OA\Response(
     *          response=200, description="Email sent.",
     *          @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *      ),
     *     @OA\Response(response=422, description="Validation error.")
     * )
     * @param ForgotRequest $request
     * @param AuthService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotRequest $request, AuthService $service)
    {
        return $service->forgotPassword($request->validated());
    }

    /**
     * @OA\Post(
     *     path="/auth/change-password",
     *     summary="Changing the password.",
     *     description="Entering a new password.",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *          description="The token that comes to the user in the email.",
     *          in="query",
     *          name="token",
     *          required=true,
     *          example="ZXlKcGRpSTZJbGR5VGpoU2RXRllSM0UwYkc1MGJqRnJiRkoyYVhjOVBTSXNJblpoYkhWbElqb2llVEp5WjNkbWJEZGtSMUZQSzJaUU5UaHhORmRuUTFoWGVtNXRXV3RPYUdKdlQzTklaVE12UkVGeVp6MGlMQ0p0WVdNaU9pSXdNRFl6TjJGa05qbGtaalkyT1RVd016ZGtPVFExTTJGa01UY3lNVFZsT1RKbU5HTmhZalkyTVdRMU0yRmxabUZtWkROak5XRTVZekF3WVRGbU9UUmtJaXdpZEdGbklqb2lJbjA9"
     *      ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/NewPasswordRequest")
     *             )
     *      ),
     *     @OA\Response(
     *          response=200, description="Password changed.",
     *          @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *      ),
     *     @OA\Response(response=403, description="Link expired."),
     *     @OA\Response(response=404, description="User not found.")
     * )
     * @param NewPasswordRequest $request
     * @param AuthService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Modules\Core\Exceptions\EmailException
     */
    public function resetPassword(NewPasswordRequest $request, AuthService $service)
    {
        return $service->resetPassword($request->validated());
    }

    /**
     * @OA\Post(
     *     path="/auth/{provider}/redirect",
     *     summary="Getting a link to login via social network.",
     *     description="Remark: When creating a user, a default group is generated for its links.",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *          description="One of the providers: google | facebook",
     *          in="path",
     *          name="provider",
     *          required=true,
     *          example="google"
     *      ),
     *     @OA\Response(
     *          response=200, description="The link to the entrance. After authorization,
                    a {token} comes through it.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="redirect_url",
     *                  description="The link to login via social network.",
     *                  type="string",
     *                  example="https://accounts.google.com/o/oauth2/auth?scope=openid+profile+email&response_type=code",
     *              )
     *          )
     *      )
     * )
     * @param OauthRequest $request
     * @param AuthService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function oauth(OauthRequest $request, AuthService $service)
    {
        return $service->oauth($request->validated());
    }

    /**
     * @OA\Get(
     *     path="/auth/{provider}/callback",
     *     summary="Getting info of user and send token to auth.",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *          description="One of the providers: google | facebook",
     *          in="path",
     *          name="provider",
     *          required=true,
     *          example="google"
     *      ),
     *     @OA\Response(
     *          response=302, description="Redirect to front-end with token in cookie",
     *          @OA\JsonContent(ref="#/components/schemas/UserToken")
     *      ),
     *     @OA\Response(response=403, description="Error occurred during auth via social network."),
     * )
     * @param OauthRequest $request
     * @param AuthService $service
     * @return \Illuminate\Http\RedirectResponse
     * @throws \App\Modules\Core\Exceptions\AuthException
     */
    public function callback(OauthRequest $request, AuthService $service)
    {
        return $service->callback($request->validated());
    }
}
