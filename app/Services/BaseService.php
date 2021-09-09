<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

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
    public function validate(array &$data): bool
    {
        Validator::make($data, $this->rules(), $this->messages(), $this->attributes())
            ->validate();

        $data = Arr::only($data, array_keys($this->rules()));

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

    protected function nullOrEmptyStr($target, $key, $default = '')
    {
        return data_get($target, $key, $default) ?: $default;
    }

    protected function nullOrZero($target, $key, $default = 0)
    {
        return data_get($target, $key, $default) ?: $default;
    }
}
