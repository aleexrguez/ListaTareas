<?php

namespace App\Controller;

use App\Entity\Tarea;
use App\Entity\Lista;
use App\Service\TareaManager;
use App\Repository\TareaRepository;
use App\Repository\ListaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TareaController extends AbstractController
{
    #[Route('/lista/{id}/tarea', name: 'app_tareas_lista', requirements: ['id' => '\d+'])]
    public function tareasPorLista(int $id, TareaRepository $tareaRepository, ListaRepository $listaRepository): Response
    {
        $lista = $listaRepository->find($id);

        if (!$lista) {
            throw $this->createNotFoundException('La lista no existe.');
        }

        $tareas = $tareaRepository->findBy(['lista' => $lista]);

        return $this->render('tarea/lista_tareas.html.twig', [
            'tareas' => $tareas,
            'lista' => $lista, 
        ]);
    }

    #[Route('/lista/{id}/tarea/crear', name: 'app_crear_tarea', requirements: ['id' => '\d+'])]
    public function crear(int $id, Request $request, ListaRepository $listaRepository,TareaManager $tareaManager): Response
    {
        $lista = $listaRepository->find($id);
    
        if (!$lista) {
            throw $this->createNotFoundException('La lista no existe.');
        }

        $tarea = new Tarea();
        $descripcion = $request->request->get('descripcion', null);
        $finalizada = $request->request->get('finalizada', false);

        if (null !==$descripcion) {
            $tarea->setDescripcion($descripcion);
            $tarea->setFinalizada($finalizada ? true : false); 
            $tarea->setLista($lista); 
            $errores = $tareaManager->validar($tarea);
            if (empty($errores)){
                $tareaManager->crear($tarea);
                $this->addFlash('success', 'Tarea creada correctamente');
                return $this->redirectToRoute('app_tareas_lista', ['id' => $lista->getId()]);
            }else{
                foreach($errores as $error){
                    $this->addFlash(
                        'warning',
                        $error
                    );
                }
            }
        }
        return $this->render('tarea/crear.html.twig', [
            'tarea' => $tarea,
            'lista' => $lista, 
        ]);
    }

    #[Route('/lista/{listaId}/tarea/{id}/editar', name: 'app_editar_tarea', requirements: ['listaId' => '\d+', 'id' => '\d+'])]
    public function editarTarea(int $listaId, int $id, ListaRepository $listaRepository, TareaRepository $tareaRepository,TareaManager $tareaManager, Request $request): Response
    {
        $lista = $listaRepository->find($listaId);
        $tarea = $tareaRepository->find($id);
        if (!$lista) {
            throw $this->createNotFoundException('La lista no existe.');
        }
        if (!$tarea) {
            throw $this->createNotFoundException('La tarea no existe.');
        }
        $descripcion = $request->request->get('descripcion', null);
        $finalizada = $request->request->get('finalizada', false);
        if (!empty($descripcion)) {
            $tarea->setDescripcion($descripcion);
            $tarea->setFinalizada($finalizada ? true : false);
            $errores = $tareaManager->validar($tarea);
            if (empty($errores)){
                $tareaManager->editar($tarea);
                $this->addFlash('success', 'Tarea editada correctamente');
                return $this->redirectToRoute('app_tareas_lista', ['id' => $lista->getId()]);
            }else{
                foreach($errores as $error){
                    $this->addFlash(
                        'warning',
                        $error
                    );
                }
            }
        }
        return $this->render('tarea/editar.html.twig', [
            'tarea' => $tarea,
            'lista' => $lista,
        ]);
    }

    #[Route('/lista/{listaId}/tarea/eliminar/{id}', name: 'app_eliminar_tarea', requirements: ['listaId' => '\d+', 'id' => '\d+'])]
    public function eliminar(int $listaId, int $id, ListaRepository $listaRepository,TareaManager $tareaManager, TareaRepository $tareaRepository): Response
    {
        $lista = $listaRepository->find($listaId);
        $tarea = $tareaRepository->find($id);

        if (!$lista) {
            throw $this->createNotFoundException('La lista no existe.');
        }

        if (!$tarea) {
            throw $this->createNotFoundException('La tarea no existe.');
        }
        $tareaManager->eliminar($tarea);
        $this->addFlash('success', 'Tarea eliminada correctamente');
        return $this->redirectToRoute('app_tareas_lista', ['id' => $lista->getId()]);
        
    }

    #[Route('/tarea/{id}/finalizar', name: 'app_finalizar_tarea', methods: ['POST'])]
    public function finalizarTarea(int $id, Request $request, TareaRepository $tareaRepository, TareaManager $tareaManager): Response
    {
        $tarea = $tareaRepository->find($id);

        if (!$tarea) {
            throw $this->createNotFoundException('La tarea no existe.');
        }

        if ($request->isXmlHttpRequest()) {
            $nuevaFinalizacion = !$tarea->isFinalizada();
            $tareaManager->finalizar($tarea, $nuevaFinalizacion);

            return $this->json([
                'finalizada' => $nuevaFinalizacion
            ]);
        }

        throw $this->createNotFoundException();
    }
    #[Route('/tarea/{id}/eliminar', name: 'app_eliminar_tarea_ajax', methods: ['POST'])]
    public function eliminarTareaAjax(int $id, Request $request, TareaRepository $tareaRepository, TareaManager $tareaManager): Response
    {
        $tarea = $tareaRepository->find($id);

        if (!$tarea) {
            return $this->json(['error' => 'Tarea no encontrada'], 404);
        }

        if ($request->isXmlHttpRequest()) {
            $tareaManager->eliminar($tarea);
            return $this->json(['success' => true]);
        }

        return $this->json(['error' => 'Solicitud inválida'], 400);
    }

}
