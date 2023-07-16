<?php

namespace App\Majetek\Requests;

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
