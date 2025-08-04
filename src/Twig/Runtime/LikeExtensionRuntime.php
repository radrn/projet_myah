<?php

namespace App\Twig\Runtime;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostLikeRepository;
use Twig\Extension\RuntimeExtensionInterface;

class LikeExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private PostLikeRepository $postLikeRepository // Injection du repository pour accéder aux likes
    )
    {
    }

    // Fonction appelée dans Twig pour vérifier si un utilisateur a liké un post
    public function likedByUser(User $user, Post $post)
    {
        // On recherche un like en base de données qui correspond à cet utilisateur et ce post
        $postlike = $this->postLikeRepository->findOneBy([
            'user' => $user,
            'post' => $post
        ]);

        // Si un like est trouvé, on retourne "true", sinon "false"
        if ($postlike !== null) {
            return true;
        } else {
            return false;
        }
    }
}
