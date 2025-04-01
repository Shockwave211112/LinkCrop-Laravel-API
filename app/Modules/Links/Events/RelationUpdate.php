<?php

namespace App\Modules\Links\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RelationUpdate
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $modelName;
    public $record;
    public $relations;
    public function __construct($modelName, $record, $relations)
    {
        $this->modelName = $modelName;
        $this->record = $record;
        $this->relations = $relations;
    }
}
