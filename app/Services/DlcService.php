<?php

class DlcService
{
    public function calculerPeremption(int $dureeJours, string $dateCreation): string
    {
        $date = new DateTime($dateCreation);
        $date->add(new DateInterval("P{$dureeJours}D"));
        return $date->format('Y-m-d');
    }
}