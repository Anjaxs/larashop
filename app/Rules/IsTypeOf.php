<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsTypeOf implements Rule
{

    protected $className;

    public function __construct(string $className) {
        $this->className = $className;
    }

    /**
     * 判断验证是否通过
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $value instanceof $this->className;
    }

    /**
     * 错误信息
     *
     * @return string
     */
    public function message()
    {
        return '不是类' . $this->className . '的实现';
    }
}
