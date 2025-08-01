<?php

namespace App\Controller;

use App\Entity\PostLike;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostLikeController extends AbstractController
{
    #[Route('/post_ajax/handleLike/{id}', name: 'app_post_like')]
    public function index(
        string                 $id,
        PostRepository         $postRepository,
        PostLikeRepository     $postLikeRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new Response('Utilisateur non connecté', Response::HTTP_UNAUTHORIZED);
        }

        $post = $postRepository->find($id);
        if (!$post) {
            return new Response('Post introuvable', Response::HTTP_NOT_FOUND);
        }

        $postLike = $postLikeRepository->findOneBy([
            'user' => $user,
            'post' => $post
        ]);

        if ($postLike) {
            $entityManager->remove($postLike);
            $entityManager->flush();
            $liked = false;
        } else {
            $newLike = new PostLike();
            $newLike->setUser($user);
            $newLike->setPost($post);
            $entityManager->persist($newLike);
            $entityManager->flush();
            $liked = true;
        }
        return new Response(json_encode(['liked' => $liked, 'count' => $post->getPostLikes()->count()]), Response::HTTP_OK);
    }
}
