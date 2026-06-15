<?php

namespace App\Service;

use App\Document\UserRecord;
use App\DTO\CreateUserRecordDto;
use App\DTO\UserRecordListDto;
use App\Message\CreateUserRecordMessage;
use App\Repository\UserRecordRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\MessageBusInterface;

class UserRecordService
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly DocumentManager     $documentManager,
    )
    {
    }

    public function create(CreateUserRecordDto $dto, string $ip): void
    {
        $this->bus->dispatch(new CreateUserRecordMessage(
            $dto->getFirstName(),
            $dto->getLastName(),
            $dto->getPhoneNumbers(),
            $ip
        ));
    }

    public function getAllSorted(UserRecordListDto $dto): array
    {
        /** @var UserRecordRepository $repository */
        $repository = $this->documentManager->getRepository(UserRecord::class);

        return $repository->findAllSorted(
            $dto->getSortField(),
            $dto->getSortDirection()
        );
    }
}