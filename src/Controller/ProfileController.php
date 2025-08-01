<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile/{username?}', name: 'app_profile')]
    public function show(
        ?string            $username,
        UserRepository     $userRepository,
        PostRepository     $postRepository,
        PostLikeRepository $likeRepository,
        CommentRepository  $commentRepository
    ): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        // Si personne n'est connecté ET aucun username n'est fourni → redirection vers login
        if ($username === null && $user === null) {
            $this->addFlash('warning', 'Utilisateur non trouvé');
            return $this->redirectToRoute('app_login');
        }

        // Si un username est fourni, on récupère le profil correspondant dans la base
        if ($username !== null) {
            $user = $userRepository->findOneBy(['username' => $username]);
        }

        // Récupération des posts, likes et commentaires de l’utilisateur affiché
        $posts = $postRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
        $likes = $likeRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
        $comments = $commentRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);

        // Comptage du nombre d’abonnés et d’abonnements
        $followersCount = $user->getSubscribeFolloweds()->count();
        $followingCount = $user->getSubscribes()->count();

        // Vérifie si l'utilisateur connecté suit déjà le profil affiché
        $isFollowing = false;
        $currentUser = $this->getUser();

        // Si l’utilisateur est connecté et qu’il ne regarde pas son propre profil
        if ($currentUser !== null && $user !== null && $currentUser !== $user) {
            // On regarde s’il suit l’utilisateur affiché
            $isFollowing = $currentUser->getSubscribes()->contains($user);
        }

        // On envoie toutes les données nécessaires à la vue Twig
        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'likes' => $likes,
            'comments' => $comments,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
            'is_following' => $isFollowing,
        ]);
    }
}
