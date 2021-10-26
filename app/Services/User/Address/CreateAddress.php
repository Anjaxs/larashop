<?php

namespace App\Services\User\Address;

use App\Services\BaseService;
use App\Models\User\Address;
use App\Rules\Phone;

class CreateAddress extends BaseService
{
    public function rules()
    {
        return [
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
            'user_id'       => '客户',
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
     * 添加用户收货地址
     */
    public function execute(array $data)
    {
        $this->validate($data);

        $address = Address::create($data);

        return $address;
    }
}
