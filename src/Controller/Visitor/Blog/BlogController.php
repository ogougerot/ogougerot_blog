<?php

namespace App\Controller\Visitor\Blog;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Repository\CategoryRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository,
        private PostRepository $postRepository,
        private PaginatorInterface $paginator,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/blog', name: 'app_visitor_blog_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $categories = $this->categoryRepository->findAll();
        $tags = $this->tagRepository->findAll();
        $query = $this->postRepository->findBy(['isPublished' => true], ['publishedAt' => 'DESC']);

        $posts = $this->paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            3 /* limit per page */
        );

        return $this->render('pages/visitor/blog/index.html.twig', [
            'categories' => $categories,
            'tags' => $tags,
            'posts' => $posts,
        ]);
    }

    #[Route('/blog/articles-filtre-par-categorie/{id<\d+>}/{slug}', name: 'app_visitor_blog_filter_by_category', methods: ['GET'])]
    public function postsFilterByCategory(Category $category): Response
    {
        $posts = $this->postRepository->findBy(['category' => $category], ['isPublished' => 'DESC']);

        return $this->render('pages/visitor/blog/index.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
            'tags' => $this->tagRepository->findAll(),
            'posts' => $posts,
        ]);
    }

    #[Route('/blog/articles-filtre-par-tag/{id<\d+>}/{slug}', name: 'app_visitor_blog_filter_by_tag', methods: ['GET'])]
    public function postsFilterByTag(Tag $tag): Response
    {
        $posts = $this->postRepository->filterPostsByTag($tag->getId());

        return $this->render('pages/visitor/blog/index.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
            'tags' => $this->tagRepository->findAll(),
            'posts' => $posts,
        ]);
    }
}