<?php

namespace App\Controller;

use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UsersController extends AbstractController
{
    #[Route('/profile/{username}', name: 'app_users')]
    public function show(
        UserRepository     $userRepository,
        PostRepository     $postRepository,
        PostLikeRepository $likeRepository,

    ): Response
    {
        $user = $this->getUser();
        
        $users = $userRepository->findBy(['username']);

        if (!$user) {
            throw $this->createNotFoundException("Utilisateur non trouvé.");
        }
        $posts = $postRepository->findBy(['user' => $users], ['createdAt' => 'DESC']);
        $likes = $likeRepository->findBy(['user' => $users], ['createdAt' => 'DESC']);

        return $this->render('users/users.html.twig', [
            'users' => $users,
            'posts' => $posts,
            'likes' => $likes,
        ]);
    }
}
