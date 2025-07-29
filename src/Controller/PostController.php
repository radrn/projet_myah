<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    #[Route('/post/{username}/{id}', name: 'app_post')]
    public function show(
        string            $username,
        int               $id,
        UserRepository    $userRepository,
        PostRepository    $postRepository,
        CommentRepository $commentRepository,
    ): Response
    {
        $currentUser = $this->getUser();

        $author = $userRepository->findOneBy(['username' => $username]);
        if (!$author) {
            throw $this->createNotFoundException("Utilisateur non trouvé.");
        }

        $post = $postRepository->findOneBy([
            'id' => $id,
            'user' => $author,
        ]);
        if (!$post) {
            throw $this->createNotFoundException("Post non trouvé.");
        }

        $comment = $commentRepository->findBy(['post' => $post], ['createdAt' => 'DESC']);

        return $this->render('post/index.html.twig', [
            'post' => $post,
            'user' => $currentUser,
            'comments' => $comment,
        ]);
    }
}
