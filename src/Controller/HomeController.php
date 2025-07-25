<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\SubscribeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function show(
        PostRepository      $postRepository,
        SubscribeRepository $subscribeRepository
    ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir le fil d’actualité.');
        }

        $subscriptions = $subscribeRepository->findBy(['follower' => $user]);

        $followedUsers = [];
        foreach ($subscriptions as $subscription) {
            $followedUsers[] = $subscription->getFollowed();
        }

        $posts = $postRepository->findBy(
            ['user' => $followedUsers],
            ['createdAt' => 'DESC']
        );

        return $this->render('home/home.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'subscribe' => $subscriptions,
        ]);
    }


}
