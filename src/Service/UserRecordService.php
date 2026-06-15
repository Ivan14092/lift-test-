<?php

namespace App\Service;

use App\Document\UserRecord;
use App\DTO\CreateUserRecordDto;
use App\DTO\UserRecordListDto;
use App\DTO\UserRecordResponseDto;
use App\Message\CreateUserRecordMessage;
use App\Repository\UserRecordRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class UserRecordService
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly UserRecordRepository $repository,
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
        return array_map(
            fn(UserRecord $record) => new UserRecordResponseDto(
                $record->getId(),
                $record->getFirstName(),
                $record->getLastName(),
                $record->getPhoneNumbers(),
                $record->getIpAddress(),
                $record->getCountry(),
                $record->getCreatedAt()),
            $this->repository->findAllSorted(
                $dto->getSortField(),
                $dto->getSortDirection()
            ));
    }
}