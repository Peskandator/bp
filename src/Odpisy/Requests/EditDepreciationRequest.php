<?php

namespace App\Odpisy\Requests;

class EditDepreciationRequest
{
    public function __construct(
        public int $id,
        public float $amount,
        public float $percentage,
        public bool $executable,
    ) {
    }
}

