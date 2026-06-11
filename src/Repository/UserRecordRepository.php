<?php

namespace App\Repository;

use App\Document\UserRecord;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<UserRecord>
 */
class UserRecordRepository extends DocumentRepository
{
    public function findAllSorted(string $field, string $direction = 'asc'): array
    {
        $allowedFields = ['firstName', 'lastName', 'createdAt', 'country'];

        if (!in_array($field, $allowedFields)) {
            $field = 'createdAt';
        }

        $order = strtolower($direction) === 'desc' ? -1 : 1;

        return $this->createQueryBuilder()
            ->sort($field, $order)
            ->getQuery()
            ->execute()
            ->toArray();
    }
}