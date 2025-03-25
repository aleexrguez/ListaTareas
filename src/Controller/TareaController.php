<?php

namespace App\Controller;

use App\Entity\Tarea;
use App\Entity\Lista;
use App\Repository\TareaRepository;
use App\Repository\ListaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TareaController extends AbstractController
{
    #[Route('/lista/tarea', name: 'app_tarea')]
    public function listado(TareaRepository $tareaRepository): Response
    {
        $tareas = $tareaRepository->findAll();
        return $this->render('tarea/lista_tareas.html.twig', [
            'tareas' => $tareas,
        ]);
    }
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
    public function crear(int $id, Request $request, EntityManagerInterface $em, ListaRepository $listaRepository): Response
    {
        $lista = $listaRepository->find($id);
    
        if (!$lista) {
            throw $this->createNotFoundException('La lista no existe.');
        }

        $tarea = new Tarea();
        $descripcion = $request->request->get('descripcion', null);

        if ($descripcion && !empty($descripcion)) {
            $tarea->setDescripcion($descripcion);
            $tarea->setLista($lista); 
            $em->persist($tarea);
            $em->flush();

            $this->addFlash('success', 'Tarea creada correctamente');
            return $this->redirectToRoute('app_tareas_lista', ['id' => $lista->getId()]);
        } else {
            $this->addFlash('warning', 'El campo descripción es obligatorio');
        }

        return $this->render('tarea/crear.html.twig', [
            'tarea' => $tarea,
            'lista' => $lista, 
        ]);
    }

    #[Route('/lista/{listaId}/tarea/{id}/editar', name: 'app_editar_tarea', requirements: ['listaId' => '\d+', 'id' => '\d+'])]
    public function editarTarea(int $listaId, int $id, ListaRepository $listaRepository, TareaRepository $tareaRepository, Request $request, EntityManagerInterface $em): Response
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

        if ($descripcion !== null) {
            if (!empty($descripcion)) {
                $tarea->setDescripcion($descripcion);
                $em->persist($tarea);
                $em->flush();
                $this->addFlash('success', 'Tarea editada correctamente');
                return $this->redirectToRoute('app_tareas_lista', ['id' => $lista->getId()]);
            } else {
                $this->addFlash('warning', 'El campo descripción es obligatorio');
            }
        }

        return $this->render('tarea/editar.html.twig', [
            'tarea' => $tarea,
            'lista' => $lista,
        ]);
    }

    #[Route('/lista/{listaId}/tarea/eliminar/{id}', name: 'app_eliminar_tarea', requirements: ['listaId' => '\d+', 'id' => '\d+'])]
public function eliminar(int $listaId, int $id, ListaRepository $listaRepository, TareaRepository $tareaRepository, EntityManagerInterface $em): Response
{
    $lista = $listaRepository->find($listaId);
    $tarea = $tareaRepository->find($id);

    if (!$lista) {
        throw $this->createNotFoundException('La lista no existe.');
    }

    if (!$tarea) {
        throw $this->createNotFoundException('La tarea no existe.');
    }

    $em->remove($tarea);
    $em->flush();

    $this->addFlash('success', 'Tarea eliminada correctamente');
    
    return $this->redirectToRoute('app_tareas_lista', ['id' => $lista->getId()]);
}


}
