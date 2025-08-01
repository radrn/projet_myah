<?php

namespace App\Twig\Runtime;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostLikeRepository;
use Twig\Extension\RuntimeExtensionInterface;

class LikeExtensionRuntime implements RuntimeExtensionInterface
{


    public function __construct(
        private PostLikeRepository $postLikeRepository
    )
    {

    }

    public function likedByUser(User $user, Post $post)
    {
        $postlike = $this->postLikeRepository->findOneBy([
            'user' => $user,
            'post' => $post
        ]);

        if ($postlike !== null) {
            return true;
        } else {
            return false;
        }

    }
}
