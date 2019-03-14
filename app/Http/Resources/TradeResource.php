<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'market_id'=>$this->market_id,
            'type'=>$this->type,
            'amount'=>$this->amount,
            'price'=>$this->price,
            'last_update'=>(string)$this->updated_at,
            'fulfilled_amount'=>floatval(number_format($this->fulfilled_amount,8,'.',''))

        ];
    }
}
