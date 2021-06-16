<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Form\CategoriaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductoRepository;
use App\Repository\ValoracionRepository;
use App\Repository\CategoriaRepository;

/**
 * @Route("/categoria")
 */
class CategoriaController extends AbstractController
{
    /**
     * @Route("/", name="categoria_index", methods={"GET"})
     */
    public function index(CategoriaRepository $categoriaRepository): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_NONE')) {
            return $this->redirectToRoute('inicio');
        } else {
            return $this->render('categoria/index.html.twig', [
                'categorias' => $categoriaRepository->findAll(),
            ]);
        }
    }

    /**
     * @Route("/new", name="categoria_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('inicio');
        } else {
            $categorium = new Categoria();
            $form = $this->createForm(CategoriaType::class, $categorium);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($categorium);
                $entityManager->flush();

                return $this->redirectToRoute('inicio');
            }

            return $this->render('categoria/new.html.twig', [
                'categorium' => $categorium,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{nombre}", name="categoria_show", methods={"GET"})
     */
    public function show(Categoria $categorium, ProductoRepository $productoRepository, ValoracionRepository $valoracionRepository, CategoriaRepository $categoriaRepository): Response
    {
        $productos = $productoRepository->findByCategoria($categorium);
        $mejoresValoraciones = $valoracionRepository->findAllValoraciones();
        $idMejorValorados = array();
        foreach ($mejoresValoraciones as $mejorValoracion) {
            array_push($idMejorValorados, $mejorValoracion['productoID']);
        }

        $chollos = array();

        foreach ($idMejorValorados as $id) {
            $producto = $productoRepository->findOneById($id);
            array_push($chollos, $producto);
        }

        $mejoresCategorias = $productoRepository->findAllBestCategorias();
        $idMejorCategorias = array();
        foreach ($mejoresCategorias as $mejorCategoria) {
            array_push($idMejorCategorias, $mejorCategoria['categoriaID']);
        }

        $mejorCategorias = array();

        foreach ($idMejorCategorias as $id) {
            $categorias = $categoriaRepository->findOneById($id);
            array_push($mejorCategorias, $categorias);
        }
        return $this->render('categoria/show.html.twig', [
            'categorium' => $categorium,
            'productos' => $productos,
            'categorias' => $categoriaRepository->findAll(),
            'mejorValorados' => $chollos,
            'mejoresCategorias' => $mejorCategorias
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categoria_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Categoria $categorium): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('inicio');
        } else {
            $form = $this->createForm(CategoriaType::class, $categorium);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('categoria_index');
            }

            return $this->render('categoria/edit.html.twig', [
                'categorium' => $categorium,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}", name="categoria_delete", methods={"POST"})
     */
    public function delete(Request $request, Categoria $categorium): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('inicio');
        } else {
            if ($this->isCsrfTokenValid('delete' . $categorium->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($categorium);
                $entityManager->flush();
            }

            return $this->redirectToRoute('categoria_index');
        }
    }
}
