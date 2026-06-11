<?php

namespace App\MessageHandler;

use App\Document\UserRecord;
use App\Message\CreateUserRecordMessage;
use App\Service\GeoLocationService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateUserRecordMessageHandler
{
    public function __construct(
        private readonly DocumentManager    $documentManager,
        private readonly GeoLocationService $geoLocationService
    )
    {
    }

    public function __invoke(CreateUserRecordMessage $message): void
    {
        $country = $this->geoLocationService->getCountryByIp($message->getIpAddress());

        $userRecord = new UserRecord();
        $userRecord->setFirstName($message->getFirstName());
        $userRecord->setLastName($message->getLastName());
        $userRecord->setPhoneNumbers($message->getPhoneNumbers());
        $userRecord->setIpAddress($message->getIpAddress());
        $userRecord->setCountry($country);

        $this->documentManager->persist($userRecord);
        $this->documentManager->flush();
    }
}