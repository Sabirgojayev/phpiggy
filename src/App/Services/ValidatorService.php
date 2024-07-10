<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Validator;
use Framework\Rules\{RequiredRule, EmailRule, MinRule, InRule, MatchRule, SocialMediaURLRule};

class ValidatorService
{
    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();
        $this->validator->add('required', new RequiredRule());
        $this->validator->add('email', new EmailRule());
        $this->validator->add('min', new MinRule());
        $this->validator->add('in', new InRule());
        $this->validator->add('url', new SocialMediaURLRule());
        $this->validator->add('match', new MatchRule());
    }

    public function validateRegister(array $formData)
    {
        $this->validator->validate($formData, [
            'email' => ['required', 'email'],
            'country' => ['required', 'in:USA,Canada,Mexico'],
            'tos' => ['required'],
            'password' => ['required'],
            'confirmPassword' => ['required', 'match:password'],
            'socialMediaURL' => ['required', 'url'],
            'age' => ['required', 'min:18']
        ]);
    }

    public function validateLogin(array $formData)
    {
        $this->validator->validate($formData, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
    }
}
