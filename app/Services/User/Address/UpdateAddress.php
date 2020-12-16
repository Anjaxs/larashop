<?php

namespace App\Services\User\Address;

use App\Services\BaseService;
use App\Models\User\Address;
use App\Rules\Phone;
use Illuminate\Support\Arr;

class UpdateAddress extends BaseService
{
    public function rules()
    {
        return [
            'address_id'    => 'required|integer',
            'user_id'       => 'required|integer',
            'province'      => 'required|string',
            'city'          => 'required|string',
            'district'      => 'required|string',
            'address'       => 'required|string',
            'zip'           => 'required|integer',
            'contact_name'  => 'required|string',
            'contact_phone' => ['required', new Phone],
        ];
    }

    public function attributes()
    {
        return [
            'province'      => '省',
            'city'          => '城市',
            'district'      => '地区',
            'address'       => '详细地址',
            'zip'           => '邮编',
            'contact_name'  => '姓名',
            'contact_phone' => '电话',
        ];
    }

    /**
     * 更新用户收货地址
     */
    public function execute(array $data)
    {
        $this->validate($data);

        $address = Address::where('user_id', $data['user_id'])
            ->findOrFail($data['address_id']);

        $address->update(Arr::except($data, ['user_id', 'address_id']));

        return true;
    }
}
