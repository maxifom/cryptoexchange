<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalResource extends JsonResource
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
            'value'=>$this->value,
            'address'=>$this->address,
            'tx'=>$this->tx,
            'status'=>$this->status,
            'updated_at'=>(string)$this->updated_at,
            'coin_name'=>$this->wallet->coin->name,
            'coin_id'=>$this->wallet->coin->id
        ];
    }
}
