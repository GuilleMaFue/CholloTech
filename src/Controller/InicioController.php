<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductoRepository;
use App\Repository\ValoracionRepository;
use App\Repository\CategoriaRepository;
use Psr\Log\LoggerInterface;

class InicioController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="inicio")
     */
    public function index(ProductoRepository $productoRepository, ValoracionRepository $valoracionRepository, CategoriaRepository $categoriaRepository): Response
    {
        $mejoresValoraciones = $valoracionRepository->findAllValoraciones();
        $idMejorValorados = array();
        foreach ($mejoresValoraciones as $mejorValoracion) {
            array_push($idMejorValorados, $mejorValoracion['productoID']);
        }

        $chollos = array();

        foreach ($idMejorValorados as $id) {
            $productos = $productoRepository->findOneById($id);
            array_push($chollos, $productos);
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


        return $this->render('inicio/index.html.twig', [
            'controller_name' => 'InicioController',
            'productos' => $productoRepository->findAll(),
            'mejorValorados' => $chollos,
            'mejoresCategorias' => $mejorCategorias,
            'categorias' => $categoriaRepository->findAll()
        ]);
    }
}
