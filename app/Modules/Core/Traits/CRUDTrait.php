<?php

namespace App\Modules\Core\Traits;

use Illuminate\Http\JsonResponse;

trait CRUDTrait
{
    protected $repository;

    /**
     * @return JsonResponse
     */
    public function index()
    {
        return $this->repository->index();
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function store(array $data)
    {
        return $this->repository->store($data);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        return $this->repository->show($id);
    }

    /**
     * @param int $id
     * @param array $data
     * @param array $relations
     * @return JsonResponse
     */
    public function put(int $id, array $data, array $relations = [])
    {
        return $this->repository->put($id, $data, $relations);
    }

    /**
     * @param int $id
     * @param array $data
     * @param array $relations
     * @return JsonResponse
     */
    public function patch(int $id, array $data, array $relations = [])
    {
        return $this->repository->patch($id, $data, $relations);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}
