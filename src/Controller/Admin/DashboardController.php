<?php

namespace App\Controller\Admin;

use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use App\Repository\PublicUserRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(
        ConferenceRepository $conferenceRepo,
        UserRepository $userRepo,
        PublicUserRepository $publicUserRepo,
        CommentRepository $commentRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => [
                'conferences' => count($conferenceRepo->findAll()),
                'users' => count($userRepo->findAll()),
                'visiteurs' => count($publicUserRepo->findAll()),
                'commentaires' => count($commentRepo->findAll()),
            ],
            'charts' => [
                'conferences' => $conferenceRepo->countByMonth(),
                'commentaires' => $commentRepo->countByMonth(),
                'visiteurs' => $publicUserRepo->countByMonth(),
            ],
        ]);
    }
}
