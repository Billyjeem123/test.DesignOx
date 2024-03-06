<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProposalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
        // Get the total count of proposals
        $totalProposals = $this->count();

        // Use map to transform each proposal in the collection
        $formattedProposals = $this->map(function ($proposal) {
            return [
                'proposal_id' => $proposal->id,
                'cover_letter' => $proposal->cover_letter ?? null,
                'preferred_date' => $proposal->preferred_date ?? null,
                'applied_date' => $proposal->created_at->diffForHumans(),
                'talent_info' => [
                    'user_name' => $proposal->user->fullname,
                    'country' => $proposal->user->country // Corrected typo
                ]
            ];
        });

        // Convert the collection to an array and append the total count
        return [
            'proposals' => $formattedProposals->toArray(),
            'total_proposals' => $totalProposals,
        ];
    }


}
