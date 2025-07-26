<?php

namespace App\Controller\Visitor\Community;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommunityController extends AbstractController
{
    #[Route('/community', name: 'app_visitor_community_create', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pages/visitor/community/index.html.twig', [
            'controller_name' => 'CommunityController',
        ]);
    }
}
