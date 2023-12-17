<?php

namespace App\Controller;

use App\Connection\DoctrineMultidatabaseConnection;
use App\Entity\UserEntity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class PublicController extends AbstractController {
    #[Route('', methods: ['GET'])]
    public function getIndexAction(ManagerRegistry $doctrine, Request $request): JsonResponse {
        $user = $doctrine->getRepository(UserEntity::class)->findOneBy([
            'username' => $request->get('username')
        ]);
        if ($user) {
            return new JsonResponse(['message' => 'Hello ' . $user->getUsername()]);
        }
        return new JsonResponse(['message' => 'No user found']);
    }

    #[Route('/user', methods: ['POST'])]
    public function postUser(ManagerRegistry $doctrine, Request $request): JsonResponse {
        $user = new UserEntity(
            $request->toArray()['username']
        );
        $doctrine->getManager()->persist($user);
        $doctrine->getManager()->flush();
        return new JsonResponse(['message' => 'User created']);
    }
}
