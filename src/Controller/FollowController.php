<?php

namespace App\Controller;

use App\Entity\Subscribe;
use App\Repository\SubscribeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FollowController extends AbstractController
{
    #[Route('/profile_ajax/handleFollow/{id}', name: 'profile_follow')]
    public function handleFollow(
        int                    $id,
        UserRepository         $userRepository,
        SubscribeRepository    $subscribeRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return new Response('Utilisateur non connecté', Response::HTTP_UNAUTHORIZED);
        }
        $targetUser = $userRepository->find($id);
        if (!$targetUser) {
            return new Response('Utilisateur introuvable', Response::HTTP_NOT_FOUND);
        }
        // Empêche de se suivre soi-même
        if ($currentUser === $targetUser) {
            return new Response('Action interdite', Response::HTTP_FORBIDDEN);
        }
        // Vérifie si l’utilisateur suit déjà le profil ciblé
        $subscribe = $subscribeRepository->findOneBy([
            'follower' => $currentUser,
            'followed' => $targetUser,
        ]);

        if ($subscribe) {
            $entityManager->remove($subscribe);
            $entityManager->flush();
            $following = false;
        } else {
            $newSubscribe = new Subscribe();
            $newSubscribe->setFollower($currentUser);
            $newSubscribe->setFollowed($targetUser);
            $entityManager->persist($newSubscribe);
            $entityManager->flush();
            $following = true;
        }

        // Récupère les nouveaux compteurs
        $followersCount = $targetUser->getSubscribeFolloweds()->count(); // followers
        $followingCount = $targetUser->getSubscribes()->count();         // following

        return new Response(json_encode([
            'following' => $following,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
        ]), Response::HTTP_OK);
    }


}
