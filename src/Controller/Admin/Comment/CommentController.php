<?php

namespace App\Controller\Admin\Comment;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class CommentController extends AbstractController
{
    public function __construct(
        private CommentRepository $commentRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/comment/index', name: 'app_admin_comment_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pages/admin/comment/index.html.twig', [
            'comments' => $this->commentRepository->findAll(),
        ]);
    }

    #[Route('/comment/{id<\d+>}', name: 'app_admin_comment_activate', methods: ['POST'])]
    public function activate(Comment $comment, Request $request): Response
    {
        if (!$this->isCsrfTokenValid("activate-comment-{$comment->getId()}", $request->request->get('csrf_token'))) {
            return $this->redirectToRoute('app_admin_comment_index');
        }

        if ($comment->isActivated()) {
            $comment->setIsActivated(false);
            $comment->setActivatedAt(null);

            $this->addFlash('success', 'Ce commentaire a été retiré de la liste des commentaires');
        } else {
            $comment->setIsActivated(true);
            $comment->setActivatedAt(new \DateTimeImmutable());

            $this->addFlash('success', 'Ce commentaire a été publié.');
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_admin_comment_index');
    }

    #[Route('/comment/delete/{id<\d+>}', name: 'app_admin_comment_delete', methods: ['POST'])]
    public function delete(Comment $comment, Request $request): Response
    {
        if ($this->isCsrfTokenValid("delete-comment-{$comment->getId()}", $request->request->get('csrf_token'))) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé');
        }

        return $this->redirectToRoute('app_admin_comment_index');
    }
}