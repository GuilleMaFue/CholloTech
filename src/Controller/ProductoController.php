<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Comentarios;
use App\Entity\Valoracion;
use App\Form\Producto1Type;
use App\Form\ComentariosType;
use App\Form\ValoracionType;
use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/producto")
 */
class ProductoController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/", name="producto_index", methods={"GET"})
     */
    public function index(ProductoRepository $productoRepository): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_NONE')) {
            return $this->redirectToRoute('inicio');
        } else {
            return $this->render('producto/index.html.twig', [
                'productos' => $productoRepository->findAll(),
            ]);
        }
    }

    /**
     * @Route("/busqueda", name="producto_busqueda", methods={"POST"})
     */
    public function search(Request $request, ProductoRepository $productoRepository): Response
    {
        $data = $request->request->get('search');

        $res = $productoRepository->findAllBySearch($data);
        return $this->render('producto/search.html.twig', array(
            'productos' => $res
        ));
    }

    /**
     * @Route("/new", name="producto_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('inicio');
        } else {
            $producto = new Producto();
            $form = $this->createForm(Producto1Type::class, $producto, array(
                'update' => false,
            ));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $archivo = $form->get('imagen')->getData();
                $directorio_subida = $this->getParameter('uploads_directory');
                $nombreArchivo = md5(uniqid()) . '.' . $archivo->guessExtension();
                $archivo->move(
                    $directorio_subida,
                    $nombreArchivo
                );
                $producto->setImagen($nombreArchivo);
                $producto->setFechacreacion(new \DateTime());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($producto);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('producto_show', array('id' => $producto->getId())));
            }

            return $this->render('producto/new.html.twig', [
                'producto' => $producto,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}", name="producto_show", requirements={"id":"\d+"}, methods={"GET","POST"})
     */
    public function show(Request $request, Producto $producto): Response
    {
        $comentario = new Comentarios();
        $valoracion = new Valoracion();
        $comentarios = $this->createForm(ComentariosType::class, $comentario);
        $puntuacion = $this->createForm(ValoracionType::class, $valoracion);
        $comentarios->handleRequest($request);
        if ($comentarios->isSubmitted() && $comentarios->isValid()) {
            $comentario->setProducto($producto);
            $comentario->setUsuario($this->getUser());
            $comentario->setFechacreacion(new \DateTime());

            $this->entityManager->persist($comentario);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('producto_show', array('id' => $producto->getId())));
        }
        $haValorado = false;
        foreach ($producto->getValoracions() as $valoracion) {
            if ($valoracion->getUsuario() == $this->getUser() or $this->getUser() == null) {
                $haValorado = true;
                break;
            }
        }
        return $this->render('producto/show.html.twig', [
            'producto' => $producto,
            'comentarios' => $producto->getComentarios(),
            'comentarioform' => $comentarios->createView(),
            'puntuacionform' => $puntuacion->createView(),
            'valoracion' => $haValorado,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="producto_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Producto $producto): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('inicio');
        } else {
            $form = $this->createForm(Producto1Type::class, $producto, array(
                'update' => $producto->getImagen() == null ? false : true,
            ));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('imagen')->getData()) {
                    $archivo = $form->get('imagen')->getData();
                    $directorio_subida = $this->getParameter('uploads_directory');
                    $nombreArchivo = md5(uniqid()) . '.' . $archivo->guessExtension();
                    $archivo->move(
                        $directorio_subida,
                        $nombreArchivo
                    );
                    $producto->setImagen($nombreArchivo);
                } else {
                    $nombreeArchivoActual = $producto->getImagen();
                    $producto->setImagen($nombreeArchivoActual);
                }
                $this->getDoctrine()->getManager()->flush();

                return $this->redirect($this->generateUrl('producto_show', array('id' => $producto->getId())));
            }

            return $this->render('producto/edit.html.twig', [
                'producto' => $producto,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}/delete", name="producto_delete", methods={"POST"})
     */
    public function delete(Request $request, Producto $producto): Response
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('inicio');
        } else {
            if ($this->isCsrfTokenValid('delete' . $producto->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($producto);
                $entityManager->flush();
            }

            return $this->redirectToRoute('inicio');
        }
    }
}
