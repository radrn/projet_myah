<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\LikeExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LikeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        /**
         * Enregistre la fonction Twig "is_post_liked" et précise à Twig que
         * son exécution se fait avec la méthode "isPostLiked" de la classe Runtime.
         */
        return [
            new TwigFunction(
                'is_post_liked',                                  // Nom de la fonction dans Twig
                [LikeExtensionRuntime::class, 'likedByUser']  // Classe Runtime + méthode associée
            ),
        ];
    }
}
