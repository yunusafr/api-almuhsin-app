<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeeCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'invoice_type' => $this->invoice_type,
            'default_amount' => $this->default_amount,
            'default_description' => $this->default_description,
            'created_at' => $this->created_at,
        ];
    }
}
