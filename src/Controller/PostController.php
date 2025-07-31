<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AddCommentType;
use App\Form\AddPostType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    #[Route('/post/{username}/{id}', name: 'app_post')]
    public function show(
        string                 $username,
        int                    $id,
        Request                $request,
        EntityManagerInterface $entityManager,
        UserRepository         $userRepository,
        PostRepository         $postRepository,
        CommentRepository      $commentRepository,

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


        $newComment = new Comment();
        $form = $this->createForm(AddCommentType::class, $newComment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newComment->setUser($this->getUser());
            $newComment->setPost($post);
            $entityManager->persist($newComment);
            $entityManager->flush();

            return $this->redirectToRoute('app_post', ['username' => $username, 'id' => $id]);
        }

        return $this->render('post/index.html.twig', [
            'post' => $post,
            'user' => $currentUser,
            'comments' => $comment,
            'comForm' => $form->createView()
        ]);
    }
}
