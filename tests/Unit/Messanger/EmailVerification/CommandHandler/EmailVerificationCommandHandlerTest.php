<?php

declare(strict_types=1);

namespace App\Tests\Unit\Messanger\EmailVerification\CommandHandler;

use App\Entity\Email;
use App\Entity\EmailVerification;
use App\Factory\EmailVerificationFactory;
use App\Messenger\EmailVerification\CommandHandler\EmailVerificationCommandHandler;
use App\Messenger\Message\EmailMessage;
use App\Repository\EmailRepository;
use App\Repository\EmailVerificationRepository;
use App\Service\EmailVerificationClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EmailVerificationCommandHandlerTest extends TestCase
{
    private const ID = 1;
    private const EMAIL = 'john.doe@example.com';
    private EmailRepository|MockObject $emailRepository;
    private EmailVerificationRepository|MockObject $emailVerificationRepository;
    private EmailVerificationClient|MockObject $client;
    private EmailVerificationFactory|MockObject $factory;
    private Email|MockObject $email;
    private EmailVerification|MockObject $emailVerification;

    protected function setUp(): void
    {
        $this->emailRepository = $this->createMock(EmailRepository::class);
        $this->emailVerificationRepository = $this->createMock(EmailVerificationRepository::class);
        $this->client = $this->createMock(EmailVerificationClient::class);
        $this->factory = $this->createMock(EmailVerificationFactory::class);
        $this->email = $this->createMock(Email::class);
        $this->emailVerification = $this->createMock(EmailVerification::class);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testHandle(array $returned): void
    {
        $message = new EmailMessage(self::ID);

        $this->emailRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => self::ID])
            ->willReturn($this->email);

        $this->email->expects(self::once())
            ->method('getEmail')
            ->willReturn(self::EMAIL);

        $this->client->expects(self::once())
            ->method('verify')
            ->willReturn($returned);

        $this->factory->expects(self::once())
            ->method('factory')
            ->willReturn($this->emailVerification);

        $this->emailVerification->expects(self::once())
            ->method('setResult')
            ->with($returned['result'])
            ->willReturn($this->emailVerification);
        $this->emailVerification->expects(self::once())
            ->method('setCreatedAt')
            ->willReturn($this->emailVerification);
        $this->emailVerification->expects(self::once())
            ->method('setIsCatchall')
            ->with($returned['catchall'])
            ->willReturn($this->emailVerification);
        $this->emailVerification->expects(self::once())
            ->method('setIsDisposable')
            ->with($returned['disposable'])
            ->willReturn($this->emailVerification);
        $this->emailVerification->expects(self::once())
            ->method('setIsDnsValidMx')
            ->with($returned['dnsValidMx'])
            ->willReturn($this->emailVerification);
        $this->emailVerification->expects(self::once())
            ->method('setIsFreemail')
            ->with($returned['freemail'])
            ->willReturn($this->emailVerification);
        $this->emailVerification->expects(self::once())
            ->method('setIsPrivate')
            ->with($returned['isPrivate'])
            ->willReturn($this->emailVerification);
        $this->emailVerification->expects(self::once())
            ->method('setIsRolebased')
            ->with($returned['rolebased'])
            ->willReturn($this->emailVerification);
        $this->emailVerification->expects(self::once())
            ->method('setIsSmtpValid')
            ->with($returned['smtpValid'])
            ->willReturn($this->emailVerification);

        $this->email->expects(self::once())
            ->method('addEmailVerification');
        // TODO strict param validation

        $this->email->expects(self::once())
            ->method('setLastVerifiedAt')
            ->with(self::anything());
        // TODO use Carbon

        $this->emailVerificationRepository->expects(self::once())
            ->method('add')
            ->with(self::anything(), true);

        $handler = new EmailVerificationCommandHandler(
            $this->emailRepository,
            $this->factory,
            $this->emailVerificationRepository,
            $this->client,
        );
        $handler($message);
    }

    private function dataProvider(): array
    {
        return [
            [[
                'result' => 'OK',
                'catchall' => true,
                'disposable' => false,
                'dnsValidMx' => true,
                'freemail' => true,
                'isPrivate' => true,
                'rolebased' => false,
                'smtpValid' => true,
            ]],
        ];
    }
}
