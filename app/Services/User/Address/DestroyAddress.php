<?php

namespace App\Services\User\Address;

use App\Services\BaseService;
use App\Models\User\Address;

class DestroyAddress extends BaseService
{
    public function rules()
    {
        return [
            'address_id'    => 'required|integer',
            'user_id'       => 'required|integer',
        ];
    }

    /**
     * 删除用户收货地址
     */
    public function execute(array $data)
    {
        $this->validate($data);

        $address = Address::where('user_id', $data['user_id'])
            ->findOrFail($data['address_id']);

        $address->delete();

        return true;
    }
}
