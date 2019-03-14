<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MarketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'base_coin' => $this->base_coin->name,
            'trade_coin' => $this->trade_coin->name,
            'volume_base' => $this->volume_base,
            'volume_trade' => $this->volume_trade,
            'trade_24hrs' => $this->trade_count_24hrs,
            'stats_24hrs'=>[
                'open'=>$this->open,
                'high'=>$this->high,
                'low'=>$this->low,
                'close'=>$this->close,
            ],
            'last_price'=>$this->last_price
        ];
    }
}
