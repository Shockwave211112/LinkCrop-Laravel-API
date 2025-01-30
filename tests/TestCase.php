<?php

namespace Tests;

use App\Modules\Links\Models\Group;
use App\Modules\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    protected function setUpTraits(): void
    {
        parent::setUpTraits();
        Redis::command('flushdb');
        $this->artisan('db:seed');
    }

    /**Обёртка для тестирования.
     *
     * @param $method -какой метод использовать
     * @param string $uri -какой путь использовать
     * @param int $status -какой ожидать статус у ответа. Дефолтно = 200
     * @param array $data -массив данных, который нужно передать [Request body]
     * @param bool $needAuth -нужна ли авторизация для действия. Дефолтно = да
     * @param string $role -какую роль использовать при авторизации. Дефолтно - админ
     * @param User|null $user -если нужно выполнить действие от лица конкретного пользователя. Дефолтно = нет
     * @return \Illuminate\Testing\TestResponse
     */
    public function defaultTest(
        $method,
        string $uri,
        int    $status = Response::HTTP_OK,
        array  $data = [],
        bool   $needAuth = true,
        string $role = 'admin',
        User   $user = null,
    )
    {
        if ($needAuth) {
            if (!$user) {
                $user = User::role($role)->first();
            }
            else {
                $user->password = '11111111';
                $user->update();
            }
            $userData = [
                'email' => $user->email,
                'password' => '11111111',
            ];

            $authToken = $this->post('/auth/login', $userData)->assertOk()->json('token');

            $authHeader = array_merge(['authorization' => 'Bearer '. $authToken]);
        } else $authHeader = [];

        return $this->json($method, $uri, $data, headers: $authHeader)->assertStatus($status);
    }
}
