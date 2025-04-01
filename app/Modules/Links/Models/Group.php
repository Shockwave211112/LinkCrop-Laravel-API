<?php

namespace App\Modules\Links\Models;

use App\Modules\Links\Factories\GroupFactory;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @OA\Schema(
 *      schema="Group",
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          example="Example group"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          type="string",
 *          example="Example description"
 *      ),
 *      @OA\Property(
 *          property="count",
 *          type="integer",
 *          example="0"
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
 *      schema="GroupsListPaginated",
 *      @OA\Property(
 *          property="current_page",
 *          type="integer",
 *          example="1"
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          @OA\Items(ref="#/components/schemas/Group")
 *      ),
 *      @OA\Property(
 *          property="first_page_url",
 *          type="string",
 *          example="http://links.com/groups?page=1"
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
 *          example="http://links.com/groups?page=2"
 *      ),
 *      @OA\Property(
 *          property="links",
 *          type="array",
 *          @OA\Items(
 *              type="object",
 *              @OA\Property(
 *                  property="url",
 *                  type="string",
 *                  example="null | http://links.com/groups?page=2"
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
 *          example="null | http://links.com/groups?page=2"
 *      ),
 *      @OA\Property(
 *          property="path",
 *          type="string",
 *          example="http://links.com/groups"
 *      ),
 *      @OA\Property(
 *          property="per_page",
 *          type="integer",
 *          example="10"
 *      ),
 *      @OA\Property(
 *          property="prev_page_url",
 *          type="string",
 *          example="null | http://links.com/groups?page=2"
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
 *
 * @OA\Schema(
 *      schema="GroupListAll",
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          @OA\Items(ref="#/components/schemas/Group")
 *      ),
 * )
 */
class Group extends Model
{
    use HasFactory;

    public $defaultRelations = [
        'user_id' => 'users',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'count'
    ];

    public function links(): BelongsToMany
    {
        return $this->belongsToMany(Link::class, 'link_groups', 'group_id', 'link_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users', 'group_id', 'user_id')->withPivot(['user_id']);
    }

    /**
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return GroupFactory::new();
    }
}
