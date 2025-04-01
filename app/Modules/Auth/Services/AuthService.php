<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Events\PasswordResetting;
use App\Modules\Auth\Events\UserRegistered;
use App\Modules\Auth\Models\ConfirmLinks;
use App\Modules\Auth\Models\RestoreLinks;
use App\Modules\Auth\Models\SocialNetwork;
use App\Modules\Core\Exceptions\AuthException;
use App\Modules\Core\Exceptions\EmailException;
use App\Modules\Links\Models\Group;
use App\Modules\Users\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Session;

class AuthService
{
    /**
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthException
     */
    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            throw new AuthException(message:__('errors.users.not_found'), status: '404');
        }

        if (!$user->password) {
            throw new AuthException(message: __('errors.auth.social_registered'), status: '403');
        }

        if (!Hash::check($data['password'], $user->password)) {
            throw new AuthException(message: __('errors.auth.mismatch'), status: '403');
        }

        return response()->json([
            'token' => $user->createToken('auth')->plainTextToken,
        ]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function registration(array $data)
    {
        $user = User::create($data);

        $user->assignRole(User::BASIC_USER);

        event(new UserRegistered($user, $this->generateUrl($user, 'verify')));

        return response()->json([
            'token' => $user->createToken('auth')->plainTextToken,
        ]);
    }

    /**
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     * @throws EmailException
     */
    public function resend($user)
    {
        if ($user->hasVerifiedEmail()) {
            throw new EmailException(message: __('errors.auth.email_already_verified'), status: '403');
        }

        event(new UserRegistered($user, $this->generateUrl($user, 'verify')));

        return response()->json([
            'message' => __('messages.auth.email_sent'),
        ]);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     * @throws EmailException
     */
    public function emailVerify($data)
    {
        $confirmLink = ConfirmLinks::where('token', $data['token'])->first();
        if (!$confirmLink) {
            throw new EmailException(message: __('errors.auth.expired'), status: '403');
        }

        $decryptedToken = decrypt(base64_decode($data['token']));
        $splittedToken = explode('_', $decryptedToken);

        $expiryDate = $splittedToken[0];

        if ($expiryDate < now()) {
            throw new EmailException(message: __('errors.auth.expired'), status: '403');
        }

        $userId = $splittedToken[1];
        $user = User::where('id', $userId)->first();
        if (!$user) {
            throw new EmailException(message: __('errors.users.not_found'), status: '404');
        }

        $user->markEmailAsVerified();
        ConfirmLinks::where('user_id', $userId)->delete();

        return response()->json([
//            'token' => $user->createToken('auth')->plainTextToken,
            'message' => __('messages.auth.email_verified'),
        ]);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword($data)
    {
        $user = User::where('email', $data['email'])->first();

        event(new PasswordResetting($user, $this->generateUrl($user, 'password')));

        return response()->json([
            'message' => __('messages.auth.email_sent'),
        ]);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     * @throws EmailException
     */
    public function resetPassword($data)
    {
        $restoreLink = RestoreLinks::where('token', $data['token'])->first();
        if (!$restoreLink) {
            throw new EmailException(message: __('errors.auth.expired'), status: '403');
        }

        $decryptedToken = decrypt(base64_decode($data['token']));
        $splittedToken = explode('_', $decryptedToken);

        $expiryDate = $splittedToken[0];
        if ($expiryDate < now()) {
            throw new EmailException(message: __('errors.auth.expired'), status: '403');
        }

        $userId = $splittedToken[1];
        $user = User::where('id', $userId)->first();
        if (!$user) {
            throw new EmailException(message: __('errors.users.not_found'), status: '404');
        }

        $user->password = $data['password'];
        $user->update();
        RestoreLinks::where('user_id', $userId)->delete();

        return response()->json([
            'message' => __('messages.auth.password_changed'),
        ]);
    }

    /**
     * @param User $user
     * @param string $type
     * @return string
     */
    private function generateUrl(User $user, string $type): string
    {
        switch ($type) {
            case 'verify':
                $minutes = config('auth.email_verify_timeout');
                $linkClass = ConfirmLinks::class;
                $route = 'email.verify';
                break;
            case 'password':
                $minutes = config('auth.password_timeout');
                $linkClass = RestoreLinks::class;
                $route = 'password.reset';
                break;
        }

        $expiryDate = now()->addMinutes($minutes);
        $token = base64_encode(encrypt($expiryDate . '_' . $user->id));

        $linkClass::create(['user_id' => $user->id, 'token' => $token, 'expiry_date' => $expiryDate]);
        return config('app.front_url') . route($route, ['token' => $token], false);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function oauth($data)
    {
        return response()->json([
            'redirect_url' => Socialite::driver($data['provider'])->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    /**
     * @param $data
     * @return RedirectResponse
     * @throws AuthException
     */
    public function callback($data)
    {
        $socialData = Socialite::driver($data['provider'])->stateless()->user();
        if (!$socialData)
            throw new AuthException(message: __('errors.auth.social', ['provider' => $data['provider']]), status: '403');

        $user = User::where('email', $socialData->getEmail())->first();
        if (!$user) {
            $user = User::create([
                'name' => $socialData->getName(),
                'email' => $socialData->getEmail()
            ]);
            $user->assignRole(User::BASIC_USER);
            $user->markEmailAsVerified();
        }

        $socialNetwork = SocialNetwork::where('social_id', $socialData->getId())->first();
        if (!$socialNetwork) {
            SocialNetwork::create([
                'user_id' => $user->id,
                'provider' => $data['provider'],
                'social_id' => $socialData->getId(),
                'raw_data' => json_encode($socialData),
            ]);
        };

        return Redirect::to(config('app.front_url') . "/auth/callback")->cookie(
            'token',
            $user->createToken('auth')->plainTextToken,
            180,
            '/',
            null,
            false,
            false
        );
    }
}
