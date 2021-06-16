<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\ComentariosRepository;
use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/usuario")
 */
class UsuarioController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/{id}", name="usuario_show", methods={"GET"})
     */
    public function show(Usuario $usuario, ComentariosRepository $comentariosRepository, ProductoRepository $productoRepository): Response
    {
        $comentarios = $comentariosRepository->findBy(array('usuario' => $usuario));
        $idComentarios = array();
        foreach ($comentarios as $comentario) {
            array_push($idComentarios, $comentario->getProducto()->getId());
        }


        $chollos = array();

        foreach ($idComentarios as $id) {
            $producto = $productoRepository->findOneById($id);
            array_push($chollos, $producto);
        }

        return $this->render('usuario/show.html.twig', [
            'usuario' => $usuario,
            'comentarios' => $comentarios,
            'chollos' => $productoRepository->findBy(array('usuario' => $usuario)),
            'cholloscomentarios' => $chollos
        ]);
    }
}