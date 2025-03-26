<?php

namespace App\Service;

Use App\Entity\Lista;
use App\Repository\ListaRepository;
use Doctrine\ORM\EntityManagerInterface;

class ListaManager
{
    private $em;
    private $listaRepository;

    public function __construct(ListaRepository $listaRepository,EntityManagerInterface $em){
        $this->em =$em;
        $this->listaRepository =$listaRepository;
    }

    public function crear(Lista $lista){
        $this->em->persist($lista);
        $this->em->flush();
    }

    public function editar(Lista $lista): void{
        $this->em->flush();
    }

    public function eliminar(Lista $lista): void{
        $this->em->remove($lista);
        $this->em->flush();
    }

    public function validar(Lista $lista){
        $errores = [];
        if (empty($lista->getTitulo()))
            $errores[] = "Campo 'titulo' obligatorio";
        return $errores;
    }
    
}