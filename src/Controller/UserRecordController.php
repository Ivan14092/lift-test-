<?php

namespace App\Controller;

use App\DTO\CreateUserRecordDto;
use App\DTO\UserRecordListDto;
use App\Service\UserRecordService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/users', name: 'api_users_')]
class UserRecordController extends AbstractController
{
    public function __construct(
        private readonly UserRecordService $userRecordService, private readonly SerializerInterface $serializer
    )
    {
    }

    #[Route('', name: 'create', defaults: ['_format' => 'json'], methods: ['POST'])]
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
            new OA\Response(response: 202, description: 'Record accepted for processing'),
            new OA\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function create(
        #[MapRequestPayload] CreateUserRecordDto $dto,
        Request                                  $request
    ): JsonResponse
    {
        $ip = $request->getClientIp() ?? '8.8.8.8';
        $this->userRecordService->create($dto, $ip);

        return $this->json([
            'message' => 'Record is being processed'
        ], Response::HTTP_ACCEPTED);
    }

    #[Route('', name: 'list', defaults: ['_format' => 'json'], methods: ['GET'])]
    #[OA\Get(
        path: '/api/users',
        summary: 'Get all user records with optional sorting',
        parameters: [
            new OA\Parameter(name: 'sort', in: 'query', required: false,
                schema: new OA\Schema(type: 'string', enum: ['firstName', 'lastName', 'createdAt', 'country'], default: 'createdAt')
            ),
            new OA\Parameter(name: 'direction', in: 'query', required: false,
                schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'asc')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of user records'),
        ]
    )]
    public function list(
        #[MapQueryString] UserRecordListDto $dto = new UserRecordListDto()
    ): JsonResponse
    {
        return $this->json(
            $this->serializer->serialize($this->userRecordService->getAllSorted($dto), "json" ),
        );
    }
}