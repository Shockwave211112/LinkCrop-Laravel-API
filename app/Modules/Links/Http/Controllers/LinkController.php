<?php

namespace App\Modules\Links\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Exceptions\AuthException;
use App\Modules\Core\Exceptions\DataBaseException;
use App\Modules\Core\Traits\PermissionsTrait;
use App\Modules\Links\Http\Requests\LinkPatchRequest;
use App\Modules\Links\Http\Requests\LinkPutRequest;
use App\Modules\Links\Http\Requests\LinkStoreRequest;
use App\Modules\Links\Services\GroupService;
use App\Modules\Links\Services\LinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    use PermissionsTrait;

    public static $permissions = [
        ['name' => 'link--list', 'description' => 'Viewing a list of links'],
        ['name' => 'link--create', 'description' => 'Creating a new link'],
        ['name' => 'link--put', 'description' => 'Editing link data'],
        ['name' => 'link--patch', 'description' => 'Partial editing link data'],
        ['name' => 'link--delete', 'description' => 'Deleting a link'],
        ['name' => 'link--show', 'description' => 'Link information by ID']
    ];

    public function __construct()
    {
        $this->__constructPermissions();
    }

    /**
     * @OA\Get(
     *     path="/links",
     *     summary="Get paginated list of links.",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *           description="Page of links",
     *           in="query",
     *           name="page",
     *           required=false,
     *           example="page=1"
     *       ),
     *     @OA\Parameter(
     *           description="Sorting order",
     *           in="query",
     *           name="orderBy",
     *           required=false,
     *           example="orderBy=id"
     *       ),
     *      @OA\Parameter(
     *            description="Sorting direction",
     *            in="query",
     *            name="dir",
     *            required=false,
     *            example="dir=desc"
     *        ),
     *      @OA\Parameter(
     *            description="Display links filtered by groups",
     *            in="query",
     *            name="groups",
     *            required=false,
     *            example="groups[]=1&groups[]=2"
     *        ),
     *      @OA\Parameter(
     *            description="Search string",
     *            in="query",
     *            name="search",
     *            required=false,
     *            example="search=Yandex"
     *        ),
     *      @OA\Parameter(
     *            description="Column for searching",
     *            in="query",
     *            name="searchBy",
     *            required=false,
     *            example="searchBy=name"
     *        ),
     *     @OA\Response(
     *          response=200, description="Paginated list.",
     *          @OA\JsonContent(ref="#/components/schemas/LinksListPaginated")
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error.")
     * )
     * @PermissionGuard link--list
     * @param LinkService $service
     * @return JsonResponse
     */
    public function index(LinkService $service)
    {
        return $service->index();
    }

    /**
     * @OA\Post(
     *     path="/links",
     *     summary="Create new link.",
     *     description="Remark: If you do not specify the groups, the default one will be automatically set.",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/StoreLinkRequest")
     *             )
     *      ),
     *     @OA\Response(
     *          response=200, description="Paginated list.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Info message."),
     *              @OA\Property(property="entity", ref="#/components/schemas/Link"))
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=422, description="Validation error."),
     * )
     * @PermissionGuard link--create
     * @param LinkStoreRequest $request
     * @param LinkService $service
     * @return JsonResponse
     * @throws AuthException
     * @throws DataBaseException
     */
    public function store(LinkStoreRequest $request, LinkService $service)
    {
        $user = auth()->user();
        $data = $request->validated();

        $data['user_id'] = $user->id;
        $data['referral'] = $service->generateReferral();

        if (!isset($data['group_id'])) $data['group_id'] = [$user->groups->pluck('id')[0]];
        $service->checkStorePermissions($user, $data);

        foreach ($data['group_id'] as $group) {
            $service->groupCountIncrement($group);
        }

        return $service->store($data);
    }

    /**
     * @OA\Get(
     *     path="/links/{id}",
     *     summary="Show link by id.",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          description="ID of link.",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1"
     *      ),
     *      @OA\Parameter(
     *          description="Type of statistic sort (asc | desc).",
     *          in="query",
     *          name="sort",
     *          example="asc"
     *      ),
     *     @OA\Parameter(
     *          description="Sorting statistic by: (day | month | year | hits). Nothing = deafult = full date.",
     *          in="query",
     *          name="by",
     *          example="day"
     *      ),
     *     @OA\Response(
     *          response=200, description="Link object.",
     *          @OA\JsonContent(
     *              @OA\Property(property="entity", ref="#/components/schemas/Link"),
     *              @OA\Property(property="stats", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="date", type="datetime", example="2024-10-29 00:00:00"),
     *                      @OA\Property(property="hits", type="integer", example="1")
     *                  )),
     *              )
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=404, description="Link not found."),
     * )
     * @PermissionGuard link--show
     * @param int $id
     * @param LinkService $service
     * @return JsonResponse
     * @throws AuthException
     * @throws DataBaseException
     */
    public function show(Request $request, int $id, LinkService $service)
    {
        $service->exists($id);
        $service->hasAccess(auth()->user(), $id);

        return $service->show($id, $request);
    }

    /**
     * @OA\Put(
     *     path="/links/{id}",
     *     summary="Update all link data.",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          description="ID of link.",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1"
     *      ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/PutLinkRequest")
     *             )
     *      ),
     *     @OA\Response(
     *          response=200, description="Link updated.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Info message."),
     *              @OA\Property(property="entity", ref="#/components/schemas/Link"),
     *              @OA\Property(property="old_entity", ref="#/components/schemas/Link"))
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=404, description="Link not found."),
     *     @OA\Response(response=422, description="Validation error."),
     * )
     * @PermissionGuard link--put
     * @param int $id
     * @param LinkPutRequest $request
     * @param LinkService $linkService
     * @param GroupService $groupService
     * @return JsonResponse
     * @throws AuthException
     * @throws DataBaseException
     */
    public function put(int $id, LinkPutRequest $request, LinkService $linkService, GroupService $groupService)
    {
        $data = $request->validated();

        $linkService->exists($id);
        $linkService->hasAccess(auth()->user(), $id);

        if (isset($data['group_id'])) {
            foreach ($data['group_id'] as $groupId) {
                $groupService->hasAccess(auth()->user(), $groupId);
            }

            $relations = [
                'groups' => $data['group_id']
            ];
            unset($data['group_id']);
        }
        else $relations = [];

        return $linkService->put($id, $data, $relations);
    }

    /**
     * @OA\Patch(
     *     path="/links/{id}",
     *     summary="Partly update link data.",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          description="ID of link.",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1"
     *      ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/PatchLinkRequest")
     *             )
     *      ),
     *     @OA\Response(
     *          response=200, description="Link updated.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Info message."),
     *              @OA\Property(property="entity", ref="#/components/schemas/Link"),
     *              @OA\Property(property="old_entity", ref="#/components/schemas/Link"))
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=404, description="Link not found."),
     *     @OA\Response(response=422, description="Validation error."),
     * )
     * @PermissionGuard link--patch
     * @param int $id
     * @param LinkPatchRequest $request
     * @param LinkService $linkService
     * @param GroupService $groupService
     * @return JsonResponse
     * @throws AuthException
     * @throws DataBaseException
     */
    public function patch(int $id, LinkPatchRequest $request, LinkService $linkService, GroupService $groupService)
    {
        $data = $request->validated();

        $linkService->exists($id);
        $linkService->hasAccess(auth()->user(), $id);

        if (isset($data['group_id'])) {
            foreach ($data['group_id'] as $groupId) {
                $groupService->hasAccess(auth()->user(), $groupId);
            }

            $relations = [
                'groups' => $data['group_id']
            ];
            unset($data['group_id']);
        }
        else $relations = [];

        return $linkService->patch($id, $data, $relations);
    }

    /**
     * @OA\Delete(
     *     path="/links/{id}",
     *     summary="Delete link.",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          description="ID of link.",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1"
     *      ),
     *     @OA\Response(
     *          response=200, description="Link deleted.",
     *          @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=404, description="Link not found."),
     * )
     * @PermissionGuard link--delete
     * @param int $id
     * @param LinkService $service
     * @return JsonResponse
     * @throws AuthException
     * @throws DataBaseException
     */
    public function delete(int $id, LinkService $service)
    {
        $service->exists($id);
        $service->hasAccess(auth()->user(), $id);
        $service->groupsDecrement($id);

        return $service->delete($id);
    }

    /**
     * @OA\Get(
     *     path="/l/{referral}",
     *     summary="Redirect to origin by referral.",
     *     description="Redirects to the original link, while increasing the click-through counter.",
     *     tags={"Links"},
     *     @OA\Parameter(
     *          description="Referral code.",
     *          in="path",
     *          name="referral",
     *          required=true,
     *          example="go1ZKwZNlg"
     *      ),
     *     @OA\Response(response=302, description="Successful redirect."),
     *     @OA\Response(response=404, description="Link not found."),
     * )
     * @param string $referral
     * @param LinkService $service
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws DataBaseException
     */
    public function referral(string $referral, LinkService $service)
    {
        return $service->referral($referral);
    }

    /**
     * @OA\Get(
     * path="/admin/links",
     * summary="Get paginated list of links.",
     * description="Shows all existing ones.",
     * tags={"Links"},
     * security={{"sanctum":{}}},
     * @OA\Parameter(
     * description="Page of links",
     * in="query",
     * name="page",
     * required=false,
     * example="page=1"
     * ),
     * @OA\Parameter(
     * description="Sorting order",
     * in="query",
     * name="orderBy",
     * required=false,
     * example="orderBy=id"
     * ),
     * @OA\Parameter(
     * description="Sorting direction",
     * in="query",
     * name="dir",
     * required=false,
     * example="dir=desc"
     * ),
     * @OA\Parameter(
     * description="Display links filtered by groups",
     * in="query",
     * name="groups",
     * required=false,
     * example="groups[]=1&groups[]=2"
     * ),
     * @OA\Parameter(
     * description="Search string",
     * in="query",
     * name="search",
     * required=false,
     * example="search=Yandex"
     * ),
     * @OA\Parameter(
     * description="Column for searching",
     * in="query",
     * name="searchBy",
     * required=false,
     * example="searchBy=name"
     * ),
     * @OA\Response(
     * response=200, description="Paginated list with all links.",
     * @OA\JsonContent(ref="#/components/schemas/LinksListPaginated")
     * ),
     * @OA\Response(response=401, description="Unauthenticated."),
     * @OA\Response(response=403, description="Permissions error.")
     * )
     * @PermissionGuard link--list
     * @param LinkService $service
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function adminIndex(LinkService $service) {
        return $service->adminIndex();
    }
}
