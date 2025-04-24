<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MobileNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $phone)
    {
        return self::checkMobileNumber($phone);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'شماره موبایل معتبر نیست';
    }

    private static function checkMobileNumber($phone)
    {
        $pattern = '/(?:^(009809[\d]{9}|00989[\d]{9}|\+9809[\d]{9}|\+989[\d]{9}|989[\d]{9}|9809[\d]{9}|09[\d]{9}|9[\d]{9}))$/';
        if (!preg_match($pattern, $phone)) {
            return false;
        }
        return true;
    }
}
