<?php

namespace App\Modules\Links\Models;

use App\Modules\Links\Factories\LinkFactory;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *      schema="Link",
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          example="Link example"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          type="string",
 *          example="Example of link description."
 *      ),
 *      @OA\Property(
 *          property="origin",
 *          type="string",
 *          example="https://example.url/11111"
 *      ),
 *      @OA\Property(
 *          property="referral",
 *          type="string",
 *          example="go1ZKwZNlg"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          type="datetime",
 *          example="2023-09-27T06:16:30.000000Z"
 *      ),
 *      @OA\Property(
 *          property="groups",
 *          type="array",
 *          @OA\Items(
 *               type="int",
 *               example="1"
 *           ),
 *      ),
 * )
 *
 * @OA\Schema(
 *      schema="LinksListPaginated",
 *      @OA\Property(
 *          property="current_page",
 *          type="integer",
 *          example="1"
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          @OA\Items(ref="#/components/schemas/Link")
 *      ),
 *      @OA\Property(
 *          property="first_page_url",
 *          type="string",
 *          example="http://links.com/links?page=1"
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
 *          example="http://links.com/links?page=2"
 *      ),
 *      @OA\Property(
 *          property="links",
 *          type="array",
 *          @OA\Items(
 *              type="object",
 *              @OA\Property(
 *                  property="url",
 *                  type="string",
 *                  example="null | http://links.com/links?page=2"
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
 *          example="null | http://links.com/links?page=2"
 *      ),
 *      @OA\Property(
 *          property="path",
 *          type="string",
 *          example="http://links.com/links"
 *      ),
 *      @OA\Property(
 *          property="per_page",
 *          type="integer",
 *          example="10"
 *      ),
 *      @OA\Property(
 *          property="prev_page_url",
 *          type="string",
 *          example="null | http://links.com/links?page=2"
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
class Link extends Model
{
    use HasFactory;

    public $defaultRelations = [
        'group_id' => 'groups',
        'user_id' => 'users',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'origin',
        'referral',
    ];

    protected $appends = ['groups'];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'link_groups', 'link_id', 'group_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'link_users', 'link_id', 'user_id')->withPivot(['user_id']);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(LinkStatistic::class);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasAccess(User $user): bool
    {
        return in_array($user->id, $this->users->pluck('id')->toArray());
    }

    /**
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return LinkFactory::new();
    }

    /**
     * @return int[]
     */
    public function getGroupsAttribute(): array
    {
        return $this->groups()->pluck('groups.id')->toArray();
    }
}
