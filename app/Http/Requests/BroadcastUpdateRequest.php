<?php

namespace App\Http\Requests;

class BroadcastUpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|max:255',
            'leader' => 'string|max:255',
            'description' => 'string',
            'status' => 'numeric',
            'started_at' => 'date',
            'finished_at' => 'date',
        ];
    }

}
