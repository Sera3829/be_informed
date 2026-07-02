<?php

namespace App\Controller;

use App\Entity\PublicUser;
use App\Form\RegistrationPublicFormType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterPublicController extends AbstractController
{
    #[Route('/register/public', name: 'app_register_public')]
    public function register(
        Request $request,
        UserManager $userManager
    ): Response {
        $user = new PublicUser();
        $form = $this->createForm(RegistrationPublicFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->createPublicUser($user, $form->get('plainPassword')->getData());

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_login_public');
        }

        return $this->render('register_public/index.html.twig', [
            'form' => $form,
        ]);
    }
}
