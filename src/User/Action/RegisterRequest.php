<?php

namespace App\User\Action;

class RegisterRequest
{
    public function __construct(
        public string $name,
        public string $surname,
        public string $email,
        public string $password,
        public string $passwordAgain,
        public \DateTimeInterface $registrationDate
    ) {
    }
}
