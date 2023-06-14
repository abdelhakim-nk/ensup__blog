<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Auteurs;
use App\Form\AuteursFormType;
use App\Repository\AuteursRepository;

class AuteursController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine, private readonly AuteursRepository $auteursRepository )
    {}

    #[Route('adminPanel/auteur', name: 'app_auteurs')]
    public function listAllAuteurs()
    {
        $getAllAuteurs = $this->doctrine->getRepository(Auteurs::class)->findAll();

        return $this->render('auteurs/index.html.twig', [
            'controller_name' => 'AuteursController',
            'auteurs' => $getAllAuteurs,
        ]);
    }



        // Create Auteur
        #[Route('adminPanel/auteur/createAuteur', name: 'app_auteur_create')]
        public function create(Request $request): Response
        {
    
            $auteur = new Auteurs();
            $Auteurform = $this->createForm(AuteursFormType::class, $auteur);
    
            $Auteurform->handleRequest($request);
    
            if ($Auteurform->isSubmitted() && $Auteurform->isValid()) {
    
                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($auteur);
                $entityManager->flush();
    
    
                $this->addFlash('success', 'Auteur créé avec succès');
                return $this->redirectToRoute('admin_article',[], Response::HTTP_MOVED_PERMANENTLY);
    
            }
    
            return $this->render('admin/createAuteur.html.twig', [
                'controller_name' => 'ArticlesController',
                'form' => $Auteurform->createView(),
            ]);
        }

    // Delete auteur
    #[Route('adminPanel/auteur/deleteAuteur/{id}', name: 'app_auteur_delete')]
    public function deleteAuteurById(Request $request): Response
    {
        $auteur = $this->doctrine->getRepository(Auteurs::class)->find($request->attributes->get('id'));

        $entityManager = $this->doctrine->getManager();
        $entityManager->remove($auteur);
        $entityManager->flush();

        $this->addFlash('success', 'Auteur supprimé avec succès');
        return $this->redirectToRoute('app_auteurs');
    }

    // Update auteur
    #[Route('adminPanel/auteur/updateAuteur/{id}', name: 'app_auteur_update')]
    public function updateAuteurById(Request $request): Response
    {
        $auteur = $this->doctrine->getRepository(Auteurs::class)->find($request->get('id'));

        $form = $this->createForm(AuteursFormType::class, $auteur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($auteur);
            $entityManager->flush();

            return $this->redirectToRoute('app_auteurs');
        }

        return $this->render('admin/updateAuteur.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
