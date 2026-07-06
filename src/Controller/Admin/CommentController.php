<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use App\Repository\PublicUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/commentaires', name: 'admin_comment_')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        Request $request,
        CommentRepository $repo,
        ConferenceRepository $conferenceRepo,
        PublicUserRepository $publicUserRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $criteria = [];

        $conference = $request->query->getInt('conference') ? $conferenceRepo->find($request->query->getInt('conference')) : null;
        if ($conference) {
            $criteria['conference'] = $conference;
        }

        $visiteur = $request->query->getInt('visiteur') ? $publicUserRepo->find($request->query->getInt('visiteur')) : null;
        if ($visiteur) {
            $criteria['publicUser'] = $visiteur;
        }

        return $this->render('admin/comment/index.html.twig', [
            'comments' => $repo->findBy($criteria, ['createdAt' => 'DESC']),
            'filtre_conference' => $conference,
            'filtre_visiteur' => $visiteur,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Comment $comment, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success', 'Commentaire supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_comment_index');
    }
}
