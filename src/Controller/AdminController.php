<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/list/{type}', name: 'admin_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function list(
        string             $type,
        PostRepository     $postRepository,
        UserRepository     $userRepository,
        CommentRepository  $commentRepository,
        Request            $request,
        PaginatorInterface $paginator
    ): Response
    {
        // Sélection de la bonne query selon le type
        if ($type === 'post') {
            $query = $postRepository->createQueryBuilder('p')
                ->orderBy('p.createdAt', 'DESC')
                ->getQuery();
        } elseif ($type === 'user') {
            $query = $userRepository->createQueryBuilder('u')
                ->orderBy('u.username', 'ASC')
                ->getQuery();
        } elseif ($type === 'comment') {
            $query = $commentRepository->createQueryBuilder('c')
                ->orderBy('c.createdAt', 'DESC')
                ->getQuery();
        } else {
            throw $this->createNotFoundException('Invalid type: ' . $type);
        }

        // Pagination
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin_list/index.html.twig', [
            'type' => $type,
            'items' => $pagination,
        ]);
    }


    #[Route('/admin/delete/{type}/{id}', name: 'admin_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        string                 $type,
        int                    $id,
        PostRepository         $postRepository,
        UserRepository         $userRepository,
        CommentRepository      $commentRepository,
        EntityManagerInterface $em
    ): RedirectResponse
    {
        // Fetch the item to delete
        if ($type === 'post') {
            $item = $postRepository->find($id);
        } elseif ($type === 'user') {
            $item = $userRepository->find($id);
        } elseif ($type === 'comment') {
            $item = $commentRepository->find($id);
        } else {
            throw $this->createNotFoundException('Invalid type: ' . $type);
        }

        if (!$item) {
            throw $this->createNotFoundException('Item not found.');
        }

        // Remove and persist
        $em->remove($item);
        $em->flush();

        $this->addFlash('success', ucfirst($type) . ' deleted successfully.');

        return $this->redirectToRoute('admin_list', ['type' => $type]);
    }
}

