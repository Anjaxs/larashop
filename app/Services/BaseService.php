<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

abstract class BaseService
{
    /**
     * 校验参数规则
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * 错误提示
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * 自定义属性名
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }

    /**
     * 校验
     *
     * @param array $data
     * @throws \Illuminate\Validation\ValidationException
     * @return bool
     */
    public function validate(array $data): bool
    {
        Validator::make($data, $this->rules(), $this->messages(), $this->attributes())
            ->validate();

        return true;
    }

    /**
     * 业务逻辑
     *
     * @param array $data
     * @throws \Illuminate\Validation\ValidationException
     * @return mixed
     */
    abstract public function execute(array $data);
}
