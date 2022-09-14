<?php

declare(strict_types=1);

namespace App\Messenger\EmailVerification\CommandHandler;

use App\Factory\EmailVerificationFactory;
use App\Messenger\Message\EmailMessage;
use App\Repository\EmailRepository;
use App\Repository\EmailVerificationRepository;
use App\Service\EmailVerificationClient;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use DateTimeImmutable;

#[AsMessageHandler]
class EmailVerificationCommandHandler
{
    public function __construct(
        private readonly EmailRepository             $emailRepository,
        private readonly EmailVerificationFactory    $emailVerificationFactory,
        private readonly EmailVerificationRepository $emailVerificationRepository,
        private readonly EmailVerificationClient     $client,
    ) { }

    public function __invoke(EmailMessage $command): void
    {
        $email = $this->emailRepository->findOneBy(['id' => $command->id]);
        $result = $this->client->verify($email->getEmail());

        if ($result) {
            // TODO remove self returned types
            $verification = ($this->emailVerificationFactory->factory())
                ->setResult($result['result'] ?? '')
                ->setCreatedAt(new DateTimeImmutable())
                ->setIsCatchall(boolval($result['catchall'] ?? false))
                ->setIsDisposable(boolval($result['disposable'] ?? false))
                ->setIsDnsValidMx(boolval($result['dnsValidMx'] ?? false))
                ->setIsFreemail(boolval($result['freemail'] ?? false))
                ->setIsPrivate(boolval($result['isPrivate'] ?? false))
                ->setIsRolebased(boolval($result['rolebased'] ?? false))
                ->setIsSmtpValid(boolval($result['smtpValid'] ?? false))
            ;

            $email->addEmailVerification($verification);
            $email->setLastVerifiedAt($verification->getCreatedAt());

            $this->emailVerificationRepository->add($verification, true);
        }
    }
}
