<?php

namespace App\Controller;

use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use App\Repository\SubscribeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UsersController extends AbstractController
{
    #[Route('/profile/{username}', name: 'app_users')]
    public function show(
        string              $username,
        UserRepository      $userRepository,
        PostRepository      $postRepository,
        PostLikeRepository  $likeRepository,
        SubscribeRepository $subscribeRepository
    ): Response
    {
        $user = $this->getUser();
        $users = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException("Utilisateur non trouvé.");
        }

        $posts = $postRepository->findBy(['user' => $users], ['createdAt' => 'DESC']);
        $likes = $likeRepository->findBy(['user' => $users], ['createdAt' => 'DESC']);
        $followersCount = $users->getSubscribeFolloweds()->count();
        $followingCount = $users->getSubscribes()->count();

        return $this->render('users/users.html.twig', [
            'user' => $user,
            'users' => $users,
            'posts' => $posts,
            'likes' => $likes,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
        ]);
    }

}
