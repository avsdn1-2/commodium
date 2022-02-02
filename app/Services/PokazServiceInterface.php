<?php


namespace App\Services;


interface PokazServiceInterface
{
    public function getPokaz($user,$period):array;
    public function savePokaz($user,$period,$water_prev,$warm_prev,$water,$warm):string;
}
