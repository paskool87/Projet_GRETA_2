<?php

namespace App\Enum;

enum StatusFiche: string
{
    case BROUILLON = 'BROUILLON';
    case SOUMISE = 'SOUMISE';
    case CRITERES_NON_REMPLIS = 'CRITERES_NON_REMPLIS';
    case VALIDE = 'VALIDE';
}
