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
        private LikeRepository $likeRepository,
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

    #[Route('/blog/article/{id<\d+>}/{slug}', name: 'app_visitor_blog_post_show', methods: ['GET', 'POST'])]
    public function showPost(Post $post, Request $request): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var User
             */
            $user = $this->getUser();

            $comment->setPost($post);
            $comment->setUser($user);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setActivatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_visitor_blog_post_show', [
                'id' => $post->getId(),
                'slug' => $post->getSlug(),
            ]);
        }

        return $this->render('pages/visitor/blog/show.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/blog/article/{id<\d+>}/{slug}/aime', name: 'app_visitor_blog_post_like', methods: ['GET'])]
    public function likePost(Post $post): Response
    {
        /**
         * Vérifions s'il y a un utilisateur connecté.
         *
         * @var User
         */
        $user = $this->getUser();

        // Si ce n'est pas le cas
        if (null == $user) {
            // Retourner le message d'erreur correspondant
            return $this->json(['message' => "Vous devez être connecté avant d'aimer cet article."], Response::HTTP_FORBIDDEN);
        }

        // Dans le cas contraire,

        // Vérifions si l'article a déjà été liké ou non
        // Si l'article a déjà été liké
        if ($post->isLikedBy($user)) {
            // Récupérer le like,
            $like = $this->likeRepository->findOneBy(['post' => $post, 'user' => $user]);

            // Supprimer ce like de la table des likes
            $this->entityManager->remove($like);
            $this->entityManager->flush();

            // Retourner le message correspondant ainsi que le nombre total de likes à jour de cet article.
            return $this->json([
                'message' => 'Votre like a été retiré',
                'totalLikesUpdated' => $this->likeRepository->count(['post' => $post]),
            ]);
        }

        // Dans le cas contraire,
        // Créer le nouveau like
        $like = new Like();

        $like->setPost($post);
        $like->setUser($user);
        $like->setCreatedAt(new \DateTimeImmutable());
        $like->setUpdatedAt(new \DateTimeImmutable());

        // Sauvegarder le like en base de données
        $this->entityManager->persist($like);
        $this->entityManager->flush();

        // Retourner le message correspondant ainsi que le nombre total de likes à jour de cet article.
        return $this->json([
            'message' => 'Votre like a été ajouté',
            'totalLikesUpdated' => $this->likeRepository->count(['post' => $post]),
        ]);
    }
}