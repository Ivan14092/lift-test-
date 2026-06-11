<?php

namespace App\Message;

class CreateUserRecordMessage
{
    public function __construct(
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly array  $phoneNumbers,
        private readonly string $ipAddress
    )
    {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhoneNumbers(): array
    {
        return $this->phoneNumbers;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }
}