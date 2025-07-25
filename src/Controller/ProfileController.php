<?php

namespace App\Controller;

use App\Factory\CommentFactory;
use App\Repository\CommentRepository;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function show(
        PostRepository     $postRepository,
        PostLikeRepository $likeRepository,
        CommentRepository  $commentRepository
    ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException("Utilisateur non trouvé.");
        }

        $posts = $postRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
        $likes = $likeRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
        $comments = $commentRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
        $followersCount = $user->getSubscribeFolloweds()->count();
        $followingCount = $user->getSubscribes()->count();
        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'likes' => $likes,
            'comments' => $comments,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
        ]);
    }

}
