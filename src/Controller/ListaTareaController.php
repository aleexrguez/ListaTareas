<?php

namespace App\Controller;

use App\Entity\Lista;
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

    #[Route('/lista/crear', name: 'app_crear')]
    public function crear(Request $request, EntityManagerInterface $em): Response
    {
        $lista = new Lista();
        $titulo = $request->request->get('titulo', null);

        if (null !== $titulo && !empty($titulo)) {
            $lista->setTitulo($titulo);
            $em->persist($lista);
            $em->flush();
            $this->addFlash('success', 'Lista de tareas creada correctamente');
            return $this->redirectToRoute('app_lista'); 
        } else {
            $this->addFlash('warning', 'El campo título es obligatorio');
        }

        return $this->render('lista_tarea/crear.html.twig', [
            'lista' => $lista,
        ]);
    }

    #[Route('/lista/editar-params/{id}', name: 'app_editar_con_params_convert', requirements: ['id' => '\d+'])]
    public function editarConParamsConvert(Lista $lista,ListaRepository $listaRepository,  Request $request, EntityManagerInterface $em): Response
    {

        $titulo = $request->request->get('titulo', null);
        if(null !== $titulo){
            if(!empty($titulo)){
                $lista->setTitulo($titulo);
                $em->persist($lista);
                $em->flush();
                $this->addFlash('success', 'Lista editada correctamente');
                return $this->redirectToRoute('app_lista');
            }else{
                $this->addFlash('warning', 'El campo titulo es obligatorio');
            }
        }
        return $this->render('lista_tarea/editar.html.twig', [
            'lista' => $lista,
        ]);
    }

    #[Route('/lista/eliminar/{id}', name: 'app_eliminar' , requirements: ['id' => '\d+'])]
    public function eliminar(Lista $lista, EntityManagerInterface $em): Response
    {
        $em->remove($lista);
        $em->flush();
        $this->addFlash('success', 'Lista eliminada correctamente');
        
        return $this-> redirectToRoute('app_lista');
    }

}
