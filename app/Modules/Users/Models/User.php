<?php

namespace App\Modules\Users\Models;

use App\Modules\Links\Models\Group;
use App\Modules\Links\Models\Link;
use App\Modules\Users\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @OA\Schema(
 *      schema="UserToken",
 *      @OA\Property(
 *          property="token",
 *          type="string",
 *          example="tl3waWcgC7o1oORhpZzjBnWcoz130pCgYjVWrX1f0ff6cfee"
 *      )
 * )
 *
 * @OA\Schema(
 *      schema="User",
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          example="Example"
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          example="example@mail.com"
 *      ),
 *      @OA\Property(
 *          property="email_verified_at",
 *          type="datetime",
 *          example="2023-09-27T06:16:30.000000Z"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          type="datetime",
 *          example="2023-09-27T06:16:30.000000Z"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          type="datetime",
 *          example="2023-09-27T06:16:30.000000Z"
 *      ),
 * )
 *
 * @OA\Schema(
 *      schema="UsersListPaginated",
 *      @OA\Property(
 *          property="current_page",
 *          type="integer",
 *          example="1"
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          @OA\Items(ref="#/components/schemas/User")
 *      ),
 *      @OA\Property(
 *          property="first_page_url",
 *          type="string",
 *          example="http://links.com/user?page=1"
 *      ),
 *      @OA\Property(
 *          property="from",
 *          type="integer",
 *          example="1"
 *      ),
 *      @OA\Property(
 *          property="last_page",
 *          type="integer",
 *          example="2"
 *      ),
 *      @OA\Property(
 *          property="last_page_url",
 *          type="string",
 *          example="http://links.com/user?page=2"
 *      ),
 *      @OA\Property(
 *          property="links",
 *          type="array",
 *          @OA\Items(
 *              type="object",
 *              @OA\Property(
 *                  property="url",
 *                  type="string",
 *                  example="null | http://links.com/user?page=2"
 *              ),
 *              @OA\Property(
 *                  property="label",
 *                  type="string",
 *                  example="Next &raquo"
 *              ),
 *              @OA\Property(
 *                  property="active",
 *                  type="boolean",
 *                  example="false"
 *              ),
 *          )
 *      ),
 *      @OA\Property(
 *          property="next_page_url",
 *          type="string",
 *          example="null | http://links.com/user?page=2"
 *      ),
 *      @OA\Property(
 *          property="path",
 *          type="string",
 *          example="http://links.com/user"
 *      ),
 *      @OA\Property(
 *          property="per_page",
 *          type="integer",
 *          example="10"
 *      ),
 *      @OA\Property(
 *          property="prev_page_url",
 *          type="string",
 *          example="null | http://links.com/user?page=2"
 *      ),
 *      @OA\Property(
 *          property="to",
 *          type="integer",
 *          example="10"
 *      ),
 *      @OA\Property(
 *          property="total",
 *          type="integer",
 *          example="15"
 *      )
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    public $defaultRelations = [
        'role_id' => 'roles',
    ];

    const BASIC_USER = 'basic_user';
    const ADMIN = 'admin';

    protected $guard_name = 'web';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }

    public function links(): BelongsToMany
    {
        return $this->belongsToMany(Link::class, 'link_users', 'user_id', 'link_id')->withPivot(['user_id']);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_users', 'user_id', 'group_id')->withPivot(['user_id']);
    }
}
