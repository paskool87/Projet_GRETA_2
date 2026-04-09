<?php

//ENUM("BROUILLON", "SOUMISE" , "EN_ATTENTE" , "VALIDE" )
namespace App\Enum;

enum StatusFiche: string
{
    case BROUILLON = "brouillon";
    case SOUMISE = "soumise";
    case EN_ATTENTE = "en_attente";
    case VALIDE = "valide";
}