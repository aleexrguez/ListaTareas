<?php

namespace App\Service;

use App\Entity\Tarea;
use App\Repository\TareaRepository;
use Doctrine\ORM\EntityManagerInterface;

class TareaManager
{
    private $em;
    private $tareaRepository;

    public function __construct(TareaRepository $tareaRepository,EntityManagerInterface $em){
        $this->em =$em;
        $this->tareaRepository =$tareaRepository;
    }

    public function crear(Tarea $tarea){
        $this->em->persist($tarea);
        $this->em->flush();
    }

    public function editar(Tarea $tarea): void{
        $this->em->flush();
    }

    public function eliminar(Tarea $tarea): void{
        $this->em->remove($tarea);
        $this->em->flush();
    }

    public function validar(Tarea $tarea){
        $errores = [];
        if (empty($tarea->getDescripcion()))
            $errores[] = "Campo 'descripcion' obligatorio";
        return $errores;
    }

}