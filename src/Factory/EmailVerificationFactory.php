<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\EmailVerification;

class EmailVerificationFactory
{
    public function factory(): EmailVerification
    {
        return new EmailVerification();
    }
}
