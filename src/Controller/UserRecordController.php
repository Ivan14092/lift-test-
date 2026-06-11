<?php

namespace App\Controller;

use App\Document\UserRecord;
use App\Message\CreateUserRecordMessage;
use App\Repository\UserRecordRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users', name: 'api_users_')]
class UserRecordController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly DocumentManager $documentManager
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/users',
        summary: 'Create a new user record',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['firstName', 'lastName', 'phoneNumbers'],
                properties: [
                    new OA\Property(property: 'firstName', type: 'string', example: 'Ivan'),
                    new OA\Property(property: 'lastName', type: 'string', example: 'Petrenko'),
                    new OA\Property(
                        property: 'phoneNumbers',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['+380971234567', '+380631234567']
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 202,
                description: 'Record accepted for processing',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Record is being processed'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            ),
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['firstName'], $data['lastName'], $data['phoneNumbers'])) {
            return $this->json([
                'error' => 'firstName, lastName and phoneNumbers are required'
            ], Response::HTTP_BAD_REQUEST);
        }

        $ip = $request->getClientIp() ?? '8.8.8.8';

        $this->bus->dispatch(new CreateUserRecordMessage(
            $data['firstName'],
            $data['lastName'],
            $data['phoneNumbers'],
            $ip
        ));

        return $this->json([
            'message' => 'Record is being processed'
        ], Response::HTTP_ACCEPTED);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/users',
        summary: 'Get all user records with optional sorting',
        parameters: [
            new OA\Parameter(
                name: 'sort',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['firstName', 'lastName', 'createdAt', 'country'],
                    default: 'createdAt'
                )
            ),
            new OA\Parameter(
                name: 'direction',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['asc', 'desc'],
                    default: 'asc'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of user records',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'string', example: '6a2a8ae5b0f6440b45042661'),
                            new OA\Property(property: 'firstName', type: 'string', example: 'Ivan'),
                            new OA\Property(property: 'lastName', type: 'string', example: 'Petrenko'),
                            new OA\Property(property: 'phoneNumbers', type: 'array', items: new OA\Items(type: 'string')),
                            new OA\Property(property: 'ipAddress', type: 'string', example: '172.22.0.1'),
                            new OA\Property(property: 'country', type: 'string', example: 'Ukraine'),
                            new OA\Property(property: 'createdAt', type: 'string', example: '2026-06-11 10:16:05'),
                        ]
                    )
                )
            ),
        ]
    )]
    public function list(Request $request): JsonResponse
    {
        /** @var UserRecordRepository $repository */
        $repository = $this->documentManager->getRepository(UserRecord::class);

        $sortField     = $request->query->get('sort', 'createdAt');
        $sortDirection = $request->query->get('direction', 'asc');

        $records = $repository->findAllSorted($sortField, $sortDirection);

        $data = array_map(fn(UserRecord $record) => [
            'id'           => $record->getId(),
            'firstName'    => $record->getFirstName(),
            'lastName'     => $record->getLastName(),
            'phoneNumbers' => $record->getPhoneNumbers(),
            'ipAddress'    => $record->getIpAddress(),
            'country'      => $record->getCountry(),
            'createdAt'    => $record->getCreatedAt()->format('Y-m-d H:i:s'),
        ], $records);

        return $this->json($data, Response::HTTP_OK);
    }
}