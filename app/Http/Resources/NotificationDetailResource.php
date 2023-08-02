<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $deep_link = $this->data['deep_link'];
        $parameter_value = null;
        $check_trx_id = $deep_link['parameter'] && $deep_link['parameter']['trx_id'];
        if($check_trx_id) {
            $trx_id = $deep_link['parameter']['trx_id'];
            $parameter_value = ["trx_id" => "$trx_id"];
        }
        $deep_link = [
            "target" => $deep_link['target'],
            "parameter" => $parameter_value
        ];
        
        return [
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'web_link' => $this->data['web_link'],
            'date_time' => Carbon::parse($this->created_at)->format('Y-m-d h:i:s A'),
            'deep_link' => $deep_link
        ];
    } 
}
