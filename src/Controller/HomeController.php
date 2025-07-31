<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\AddPostType;
use App\Repository\PostRepository;
use App\Repository\SubscribeRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function show(
        PostRepository         $postRepository,
        SubscribeRepository    $subscribeRepository,
        Request                $request,
        EntityManagerInterface $entityManager,
        FileUploaderService    $fileUploaderService
    ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir le fil d’actualité.');
        }

        // Récupère tous les abonnements où l’utilisateur connecté est le suiveur
        $subscriptions = $subscribeRepository->findBy(['follower' => $user]);

        // Initialise un tableau pour stocker les utilisateurs suivis
        $followedUsers = [];
        // Remplit le tableau avec les utilisateurs que l'on suit
        foreach ($subscriptions as $subscription) {
            $followedUsers[] = $subscription->getFollowed();
        }
        // Ajoute l’utilisateur lui-même pour qu’il voie aussi ses propres posts
        $followedUsers[] = $user;
        // Récupère tous les posts des utilisateurs suivis (y compris soi-même), triés par date de création décroissante
        $posts = $postRepository->findBy(
            ['user' => $followedUsers],
            ['createdAt' => 'DESC']
        );
        // Crée le formulaire d’ajout de post
        $post = new Post();
        $form = $this->createForm(AddPostType::class, $post);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide :
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère l’image uploadée depuis le formulaire
            $uploadedFile = $form->get('image')->getData();

            // Si une image est présente, on la sauvegarde avec le service d’upload
            if ($uploadedFile) {
                $filename = $fileUploaderService->uploadFile(
                    $uploadedFile,
                    '/postimg'
                );
                $post->setImage($filename);
            }
            // Attribue le post à l’utilisateur connecté
            $post->setUser($user);
            // Enregistre le post dans la base de données
            $entityManager->persist($post);
            $entityManager->flush();

            // Redirige vers la page d'accueil (évite les doublons si on recharge)
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/home.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'subscribe' => $subscriptions,
            'form' => $form->createView()
        ]);
    }


}
