<?php

namespace App\Rules;

use App\Models\User\User;
use Illuminate\Contracts\Validation\Rule;

class LoginUser implements Rule
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
        return $value instanceof User;
    }

    /**
     * 错误信息
     *
     * @return string
     */
    public function message()
    {
        return '登录用户类型不正确';
    }
}
