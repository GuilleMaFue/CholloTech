<?php

namespace App\Controller;

use App\Entity\Comentarios;
use App\Form\ComentariosType;
use App\Repository\ComentariosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comentarios")
 */
class ComentariosController extends AbstractController
{
    /**
     * @Route("/", name="comentarios_index", methods={"GET"})
     */
    public function index(ComentariosRepository $comentariosRepository): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_NONE')) {
            return $this->redirectToRoute('inicio');
        } else {
            return $this->render('comentarios/index.html.twig', [
                'comentarios' => $comentariosRepository->findAll(),
            ]);
        }
    }

    /**
     * @Route("/new", name="comentarios_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_NONE')) {
            return $this->redirectToRoute('inicio');
        } else {
            $comentario = new Comentarios();
            $form = $this->createForm(ComentariosType::class, $comentario);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $comentario->setFechacreacion(new \DateTime());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comentario);
                $entityManager->flush();

                return $this->redirectToRoute('comentarios_index');
            }

            return $this->render('comentarios/new.html.twig', [
                'comentario' => $comentario,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}", name="comentarios_show", methods={"GET"})
     */
    public function show(Comentarios $comentario): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_NONE')) {
            return $this->redirectToRoute('inicio');
        } else {
            return $this->render('comentarios/show.html.twig', [
                'comentario' => $comentario,
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="comentarios_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comentarios $comentario): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_NONE')) {
            return $this->redirectToRoute('inicio');
        } else {
            $form = $this->createForm(ComentariosType::class, $comentario);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('comentarios_index');
            }

            return $this->render('comentarios/edit.html.twig', [
                'comentario' => $comentario,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}", name="comentarios_delete", methods={"POST"})
     */
    public function delete(Request $request, Comentarios $comentario): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_NONE')) {
            return $this->redirectToRoute('inicio');
        } else {
            if ($this->isCsrfTokenValid('delete' . $comentario->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($comentario);
                $entityManager->flush();
            }

            return $this->redirectToRoute('comentarios_index');
        }
    }
}
