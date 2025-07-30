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

        $subscriptions = $subscribeRepository->findBy(['follower' => $user]);

        $followedUsers = [];
        foreach ($subscriptions as $subscription) {
            $followedUsers[] = $subscription->getFollowed();
        }

        $posts = $postRepository->findBy(
            ['user' => $followedUsers],
            ['createdAt' => 'DESC']
        );

        $post = new Post();
        $form = $this->createForm(AddPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('image')->getData();

            if ($uploadedFile) {
                $filename = $fileUploaderService->uploadFile(
                    $uploadedFile,
                    '/postimg'
                );
                $post->setImage($filename);
            }

            $post->setUser($user);
            $entityManager->persist($post);
            $entityManager->flush();

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
