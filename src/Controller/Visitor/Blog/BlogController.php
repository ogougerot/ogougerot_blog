<?php

namespace App\Controller\Visitor\Blog;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_visitor_blog_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pages/visitor/blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }
}
