<?php

namespace App\Controller\Admin\Home;

use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ContactRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class HomeController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private PostRepository $postRepository,
        private TagRepository $tagRepository,
        private CommentRepository $commentRepository,
        private UserRepository $userRepository,
        private LikeRepository $likeRepository,
    ) {
    }

    #[Route('/home', name: 'app_admin_home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pages/admin/home/index.html.twig', [
            'categories_count' => $this->categoryRepository->count(),
            'posts_count' => $this->postRepository->count(),
            'tags_count' => $this->tagRepository->count(),
            'comments_count' => $this->commentRepository->count(),
            'users_count' => $this->userRepository->count(),
            'likes_count' => $this->likeRepository->count(),
        ]);
    }
}