<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function show(
        PostRepository $postRepository,
    ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException("Utilisateur non trouvé.");
        }
    
        $posts = $postRepository->findBy(['user' => $user]);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }

}
