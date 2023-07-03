<?php

namespace App\Majetek\Action;

class CreateEntityRequest
{
    public function __construct(
        public string $name,
        public string $companyId,
        public string $country,
        public string $city,
        public string $zipCode,
        public string $street,
    ) {
    }
}
