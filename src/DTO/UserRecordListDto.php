<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UserRecordListDto
{
    public function __construct(
        #[Assert\Choice(choices: ['firstName', 'lastName', 'createdAt', 'country'])]
        public readonly string $sort = 'createdAt',

        #[Assert\Choice(choices: ['asc', 'desc'])]
        public readonly string $direction = 'asc',
    )
    {
    }

    public function getSortField(): string
    {
        return $this->sort;
    }

    public function getSortDirection(): string
    {
        return $this->direction;
    }
}