<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
        $totalReviews = $this->count();

         # Use map to transform each proposal in the collection
        $formattedReviews = $this->map(function ($review) {
            return [
                'review_id' => $review->id,
                'ratings' => $review->ratings ?? null,
                'reviews' => $review->reviews,
                'review_date' => $review->created_at->diffForHumans(),
                'user_info' => [
                    'user_name' => $review->user->fullname,
                    'country' => $review->user->country // Corrected typo
                ]
            ];
        });

        ## Convert the collection to an array and append the total count
        return [
            'reviews' => $formattedReviews->toArray(),
            'total_reviews' => $totalReviews,
        ];
    }
}
