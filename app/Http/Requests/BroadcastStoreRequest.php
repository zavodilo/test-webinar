<?php

namespace App\Http\Requests;

class BroadcastStoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'leader' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|numeric',
            'started_at' => 'required|date',
            'finished_at' => 'date',
        ];
    }

}
