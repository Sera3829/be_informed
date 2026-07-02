<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationConferencierFormType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterConferencierController extends AbstractController
{
    #[Route('/register/conferencier', name: 'app_register_conferencier')]
    public function register(
        Request $request,
        UserManager $userManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationConferencierFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->createUser($user, $form->get('plainPassword')->getData(), ['ROLE_CONFERENCIER']);

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register_conferencier/index.html.twig', [
            'form' => $form,
        ]);
    }
}
