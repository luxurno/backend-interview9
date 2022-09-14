<?php

namespace App\Service;

use App\Entity\Email;
use App\Messenger\Message\EmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailVerificationService
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) { }

    public function verify(Email $email): void
    {
        $this->commandBus->dispatch(new EmailMessage($email->getId()));
    }

    public function getVerificationCount(Email $email): int
    {
        return $email->getEmailVerifications()->count();
    }
}
