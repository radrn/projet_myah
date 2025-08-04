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
        string                 $id, // ID du post à liker/dé-liker
        PostRepository         $postRepository, // Pour récupérer le post ciblé
        PostLikeRepository     $postLikeRepository, // Pour vérifier si le like existe déjà
        EntityManagerInterface $entityManager // Pour gérer les opérations en base (persist/remove/flush)
    ): Response
    {
        // On récupère l'utilisateur connecté
        $user = $this->getUser();

        // Si aucun utilisateur n'est connecté, on retourne une erreur HTTP 401 (non autorisé)
        if (!$user) {
            return new Response('Utilisateur non connecté', Response::HTTP_UNAUTHORIZED);
        }

        // On tente de récupérer le post correspondant à l'ID
        $post = $postRepository->find($id);

        // Si le post n'existe pas, on retourne une erreur HTTP 404
        if (!$post) {
            return new Response('Post introuvable', Response::HTTP_NOT_FOUND);
        }

        // On cherche s'il existe déjà un like entre cet utilisateur et ce post
        $postLike = $postLikeRepository->findOneBy([
            'user' => $user,
            'post' => $post
        ]);

        // Cas 1 : le like existe déjà → on le supprime (dé-like)
        if ($postLike) {
            $entityManager->remove($postLike); // suppression de l'objet en attente
            $entityManager->flush(); // envoie les modifications en base
            $liked = false; // on indique que le post n'est plus liké
        } else {
            // Cas 2 : le like n'existe pas → on le crée
            $newLike = new PostLike(); // on instancie un nouveau like
            $newLike->setUser($user); // on associe l'utilisateur
            $newLike->setPost($post); // on associe le post
            $entityManager->persist($newLike); // on prépare l'enregistrement
            $entityManager->flush(); // on l'envoie en base
            $liked = true; // on indique que le post est maintenant liké
        }

        // On retourne une réponse JSON avec deux infos :
        // - liked : true/false selon l'action faite
        // - count : le nombre total de likes sur ce post après modification
        return new Response(json_encode([
            'liked' => $liked,
            'count' => $post->getPostLikes()->count()
        ]), Response::HTTP_OK);
    }
}
