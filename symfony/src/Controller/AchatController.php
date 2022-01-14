<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\Commande;
use App\Form\AchatType;
use App\Repository\AchatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/achat')]
class AchatController extends AbstractController
{
    #[Route('/', name: 'achat_index', methods: ['GET'])]
    public function index(AchatRepository $achatRepository): Response
    {
        return $this->render('achat/index.html.twig', [
            'achats' => $achatRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'achat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,Commande $commande): Response
    {
        $achat = new Achat();
        $form = $this->createForm(AchatType::class, $achat,["id"=>$commande->getRestaurant()->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $achat->setCommande($commande);
            $entityManager->persist($achat);
            $entityManager->flush();

            return $this->redirectToRoute('achat_update_prix', array("id" => $achat->getCommande()->getId()), Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('achat/new.html.twig', [
            'achat' => $achat,
            'form' => $form,
        ]);
    }

    #[Route('/updatePrix/{id}', name: 'achat_update_prix', methods: ['GET', 'POST'])]
    public function updatePrix(EntityManagerInterface $entityManager, Commande $commande): Response
    {
        $prixTotal = 0;
        if(!$commande) {
            throw $this->createNotFoundException(
                'Pas de commande trouvée avec cet id'
            );
        }
        foreach($commande->getAchats() as $ligne) {
            $quantite = $ligne->getQuantite();
            $prixProduit = $ligne->getProduit()->getPrix();
            $prixLigne = $quantite * $prixProduit;
            $prixTotal = $prixTotal + $prixLigne;
        }

        $commande->setPrix($prixTotal);

        $entityManager->flush();

        return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        //return $this->redirectToRoute('commande_show', array("id"=> $commande->getId()), Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'achat_show', methods: ['GET'])]
    public function show(Achat $achat): Response
    {
        return $this->render('achat/show.html.twig', [
            'achat' => $achat,
        ]);
    }

    #[Route('/{id}/edit', name: 'achat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Achat $achat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AchatType::class, $achat,['id'=>$achat->getCommande()->getRestaurant()->getId()]);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('achat_update_prix', array("id" => $achat->getCommande()->getId()), Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('achat/edit.html.twig', [
            'achat' => $achat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'achat_delete', methods: ['POST'])]
    public function delete(Request $request, Achat $achat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$achat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($achat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('achat_update_prix', array("id" => $achat->getCommande()->getId()), Response::HTTP_SEE_OTHER);
    }
}
