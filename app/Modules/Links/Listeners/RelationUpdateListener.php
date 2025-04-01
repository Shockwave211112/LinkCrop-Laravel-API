<?php

namespace App\Modules\Links\Listeners;

use App\Modules\Links\Events\RelationUpdate;
use App\Modules\Links\Models\Link;
use App\Modules\Links\Services\LinkService;

class RelationUpdateListener
{
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(RelationUpdate $event): void
    {
        if ($event->modelName == 'Link') {
            $linkService = new LinkService();
            $linkService->groupsUpdate($event->record->id, $event->relations['groups']);

            event('eloquent.updated: ' . Link::class, $event->record);
        }
    }
}
