<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepositResource extends JsonResource
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
            'tx'=>$this->tx,
           'value'=>$this->value,
           'confirmations'=>$this->confirmations,
           'confirmed'=>$this->confirmed,
           'tx_time'=>(string)$this->tx_time,
           'coin_name'=>$this->wallet->coin->name,
           'coin_id'=>$this->wallet->coin->id

       ];
    }
}
