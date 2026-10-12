<?php

namespace App\Http\Resources\Task;

use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\Deal\DealResource;
use App\Http\Resources\StageTaskTemplate\StageTaskTemplateResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'name'                   => $this->name,
            'description'            => $this->description,
            'client_id'              => $this->client_id,
            'deal_id'                => $this->deal_id,
            'stage_task_template_id' => $this->stage_task_template_id,
            'assigned_user_id'       => $this->assigned_user_id,
            'due_at'                 => $this->due_at?->format('Y-m-d'),
            'completed_at'           => $this->completed_at?->toIso8601String(),
            'status'                 => (int) $this->status,
            'priority'               => (int) $this->priority,
            'client'                 => ClientResource::make($this->whenLoaded('client')),
            'deal'                   => DealResource::make($this->whenLoaded('deal')),
            'task_template'          => StageTaskTemplateResource::make($this->whenLoaded('taskTemplate')),
            'assigned_user'          => $this->whenLoaded('assignedUser', function () {
                return [
                    'id'    => $this->assignedUser->id,
                    'name'  => $this->assignedUser->name,
                    'email' => $this->assignedUser->email,
                ];
            }),
            'reminders'              => TaskReminderResource::collection($this->whenLoaded('reminders')),
            'created_at'             => $this->created_at?->toIso8601String(),
            'updated_at'             => $this->updated_at?->toIso8601String(),
        ];
    }
}
