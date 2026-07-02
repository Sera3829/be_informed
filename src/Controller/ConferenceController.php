<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/conference', name: 'app_conference_')]
class ConferenceController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ConferenceRepository $repo): Response
    {
        return $this->render('conference/index.html.twig', [
            'conferences' => $repo->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(Conference $conference, Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('ROLE_USER');

            $comment->setConference($conference);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setPublicUser($this->getUser());
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire ajouté.');
            return $this->redirectToRoute('app_conference_show', ['id' => $conference->getId()]);
        }

        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
            'form' => $form,
        ]);
    }
}
