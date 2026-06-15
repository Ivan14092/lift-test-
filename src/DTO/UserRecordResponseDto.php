<?php

namespace App\DTO;

final readonly class UserRecordResponseDto
{
    public function __construct(
        private string $id,
        private string $firstName,
        private string $lastName,
        private string $phoneNumbers,
        private bool   $ipAddress,
        private bool   $country,
        private bool   $createdAt,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhoneNumbers(): string
    {
        return $this->phoneNumbers;
    }

    public function isIpAddress(): bool
    {
        return $this->ipAddress;
    }

    public function isCountry(): bool
    {
        return $this->country;
    }

    public function isCreatedAt(): bool
    {
        return $this->createdAt;
    }

}