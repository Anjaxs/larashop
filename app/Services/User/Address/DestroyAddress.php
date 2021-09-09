<?php

namespace App\Services\User\Address;

use App\Services\BaseService;
use App\Models\User\Address;

class DestroyAddress extends BaseService
{
    public function rules()
    {
        return [
            'address_id' => 'required|integer',
        ];
    }

    /**
     * 删除用户收货地址
     */
    public function execute(array $data)
    {
        $this->validate($data);

        Address::where('id', $data['address_id'])->delete();

        return true;
    }
}
