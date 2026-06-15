<?php

namespace App\Repository;

use App\Document\UserRecord;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

class UserRecordRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRecord::class);
    }

    public function findAllSorted(?string $field, string $direction = 'asc'): array
    {
        $allowedFields = ['firstName', 'lastName', 'createdAt', 'country'];

        if (!in_array($field, $allowedFields)) {
            $field = null;
        }

        $order = strtolower($direction) === 'desc' ? -1 : 1;

        $query = $this->createQueryBuilder();
        if ($field !== null) {
            $query->sort($field, $order);
        }
        return $query->getQuery()
            ->execute()
            ->toArray();
    }
}