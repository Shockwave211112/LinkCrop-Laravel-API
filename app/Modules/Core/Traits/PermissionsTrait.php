<?php

namespace App\Modules\Core\Traits;

trait PermissionsTrait
{
    /**
     * __construct
     *
     * @return void
     */
    public function __constructPermissions()
    {
        $this->resolveCommentGuards()
            ->map(function (array $item) {
                ['method' => $method, 'annotations' => $permissions] = $item;
                collect($permissions)->map(fn($permission) => $this->middleware(
                    'permission:' . $permission, [
                        'only' => is_array($method) ? $method : [$method]
                    ]
                ));
            });
    }

    /**
     * Returns a collection of [method, permissions] of controller
     * @return \Illuminate\Support\Collection
     */
    private function resolveCommentGuards()
    {
        $classReflection = new \ReflectionClass(self::class);

        return collect($classReflection->getMethods())
            ->map(function (\ReflectionMethod $Method) {
                $comment = $Method->getDocComment();
                if (!$comment) return ['method' => $Method->name, 'annotations' => []];

                $annotations = [];
                preg_match_all('#@(.*?)\n#s', $comment, $annotations);
                $annotations = $annotations[1];
                $annotations = collect($annotations)
                    ->filter(fn(string $annotation) => mb_strripos($annotation, 'PermissionGuard') !== false)
                    ->transform(fn(string $annotation) => trim(str_replace('PermissionGuard', '', $annotation)))
                    ->transform(fn(string $annotation) => explode('|', $annotation))
                    ->toArray();

                return [
                    'method' => $Method->name,
                    'annotations' => array_unique(array_merge(...$annotations))
                ];
            });
    }

}
