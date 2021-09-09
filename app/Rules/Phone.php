<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Phone implements Rule
{
    /**
     * 判断验证是否通过
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/^1[3456789]\d{9}$/', $value);
    }

    /**
     * 错误信息
     *
     * @return string
     */
    public function message()
    {
        return '手机号格式不对';
    }
}
