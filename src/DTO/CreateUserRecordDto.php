<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateUserRecordDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        private string $firstName,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        private string $lastName,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        #[Assert\Count(min: 1)]
        private array $phoneNumbers,
    ) {}

    public function getFirstName(): string { return $this->firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function getPhoneNumbers(): array { return $this->phoneNumbers; }
}