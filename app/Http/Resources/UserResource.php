<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
        'type' => 'users',
        'usertoken' => $this->id,
            'fullname' => $this->fullname,
            'phone_number' => $this->phone_number,
            'account_type' => $this->account_type,
            'google_id' => $this->google_id,
            'country' => $this->country,
            'email' => $this->email,
            'roles' => $this->roles->pluck('role_name') ,// Assuming 'role_name' is the attribute you want to include

    ];
}

}
