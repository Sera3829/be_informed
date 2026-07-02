<?php

namespace App\Controller\Conferencier;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/conferencier/dashboard', name: 'conferencier_dashboard')]
    public function index(): Response
    {
        return $this->render('conferencier/dashboard/index.html.twig', [
            'controller_name' => 'Conferencier/DashboardController',
        ]);
    }
}
