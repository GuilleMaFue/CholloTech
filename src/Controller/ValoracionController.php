<?php

namespace App\Controller;

use App\Entity\Valoracion;
use App\Entity\Producto;
use App\Form\ValoracionType;
use App\Repository\ValoracionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/valoracion")
 */
class ValoracionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/new/{id}/", name="valoracion_new", methods={"POST"})
     */
    public function new(Request $request, Producto $producto): Response
    {
        $valoracion = new Valoracion();
        $puntuacion = $request->request->get('puntuacion');
        $usuario = $this->getUser();
        $valoracion->setPuntuacion($puntuacion);
        $valoracion->setUsuario($usuario);
        $valoracion->setProducto($producto);

        $this->entityManager->persist($valoracion);
        $this->entityManager->flush();
        return new JsonResponse([
            'success'  => true,
        ]);
    }
}
