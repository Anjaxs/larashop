<?php

namespace App\Rules;

use App\Exceptions\CouponCodeUnavailableException;
use App\Models\Promotion\CouponCode;
use Illuminate\Contracts\Validation\Rule;

class ValidCouponCode implements Rule
{
    protected $errmsg = '';

    /**
     * 判断验证是否通过
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 没有传值不进行验证
        if (!$value) {
            return true;
        }
        $record = CouponCode::where('code', $value)->first();
        if (!$record) {
            $this->errmsg = '优惠券不存在';
            return false;
        }
        try {
            $record->checkAvailable();
        } catch (CouponCodeUnavailableException $e) {
            $this->errmsg = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 错误信息
     *
     * @return string
     */
    public function message()
    {
        return $this->errmsg;
    }
}
