<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TasksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => (string)$this->id,
            'attributes' => [
                'title' => ucwords($this->title),
                'description' => $this->description,
                'priority' => $this->priority->value,
                'created_at' => $this->created_at->format('d-m-Y'),
                'updated_at' => $this->updated_at->format('d-m-Y'),
            ],
            'relationships' => [
                'user' => new UserResource($this->user),
            ]
        ];
    }
}
