<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Coin extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'needed_confirmations' => $this->needed_confirmations
        ];
    }
}
