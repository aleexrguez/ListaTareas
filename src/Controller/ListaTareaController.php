<?php

namespace App\Controller;

use App\Entity\Lista;
use App\Service\ListaManager;
use App\Repository\ListaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ListaTareaController extends AbstractController
{
    #[Route('/', name: 'app_lista')]
    public function index(ListaRepository $listaRepository): Response
    {
        $listas = $listaRepository->findAll();
        return $this->render('lista_tarea/index.html.twig', [
            'listas' => $listas,
        ]);
    }

    #[Route('/lista/tarea/{id}', name: 'app_ver' , requirements: ['id' => '\d+'])]
    public function ver(Lista $lista): Response
    {
        return $this->render('tarea/lista_tareas.html.twig', [
            'lista' => $lista,
            'tareas' => $lista->getTareas(),
        ]);
    }

    #[Route('/lista/crear', name: 'app_crear')]
    public function crear(ListaManager $listaManager, Request $request): Response
    {
        $lista = new Lista();
        $titulo = $request->request->get('titulo', null);
        if(null !== $titulo){
            $lista->setTitulo($titulo);
            $errores = $listaManager->validar($lista);
            if (empty($errores)){
                $listaManager->crear($lista);
                $this->addFlash('success', 'Lista de tareas creada correctamente');
                return $this->redirectToRoute('app_lista'); 
            }else{
                foreach($errores as $error){
                    $this->addFlash(
                        'warning',
                        $error
                    );
                }
            }
        }
        return $this->render('lista_tarea/crear.html.twig', [
            'lista' => $lista,
        ]);
    }

    #[Route('/lista/editar/{id}', name: 'app_editar', requirements: ['id' => '\d+'])]
    public function editar(Lista $lista,ListaManager $listaManager,  Request $request): Response
    {
        $titulo = $request->request->get('titulo', null);
        if(!empty($titulo)){
            $lista->setTitulo($titulo);
            $errores = $listaManager->validar($lista);
            if (empty($errores)){
                $listaManager->editar($lista);
                $this->addFlash('success', 'Lista editada correctamente');
                return $this->redirectToRoute('app_lista');
            }else{
                foreach($errores as $error){
                    $this->addFlash(
                        'warning',
                        $error
                    );
                }
            }
        }
        return $this->render('lista_tarea/editar.html.twig', [
            'lista' => $lista,
        ]);
    }

    #[Route('/lista/{id}/eliminar', name: 'app_eliminar_lista', methods: ['GET', 'POST'])]
    public function eliminarLista(int $id, Request $request, ListaRepository $listaRepository, ListaManager $listaManager): Response {
        $lista = $listaRepository->find($id);

    if (!$lista) {
        return $this->json(['error' => 'Lista no encontrada'], 404);
    }

    if ($request->isXmlHttpRequest()) {
        $listaManager->eliminar($lista);
        return $this->json(['success' => true]);
    }

    return $this->json(['error' => 'Solicitud inválida'], 400);
    }
}
