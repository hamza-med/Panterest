<?php

namespace App\Controller;

use App\Repository\PinRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pin;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PinsController extends AbstractController
{
    #[Route("/", name: 'app_home')]
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }

    #[Route("/pins/create", name: 'app_pins_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;
        $form = $this->createFormBuilder($pin)
            ->add('Title', TextType::class)
            ->add('Description', TextareaType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pin);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig', ['form' => $form->createView()]);
    }



    #[Route("/pins/{id<\d+>}", name: 'app_pin_show')]
    public function show(Pin $pin): Response
    {
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    #[Route("/pins/{id<\d+>}/edit", name: 'app_pin_edit')]
    public function edit(Pin $pin, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder($pin)
            ->add('Title', TextType::class)
            ->add('Description', TextareaType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_home');
        }


        return $this->render('pins/edit.html.twig', ['pin' => $pin, 'form' => $form->createView()]);
    }
}
