<?php

namespace App\Enum;

enum Role: string
{
    case ALTERNAT            = "ALTERNANT";
    case TUTEUR              = "TUTEUR";
    case PROFESSEUR_REFERENT = "PROFESSEUR_REFERENT";
    case ADMINISTRATEUR      = "ADMINISTRATEUR";

    public function label(): string
    {
        return match ($this) {
            self::ALTERNAT            => "Alternant",
            self::TUTEUR              => "Tuteur",
            self::PROFESSEUR_REFERENT => "Professeur Référent",
            self::ADMINISTRATEUR      => "Administrateur",
        };
    }
}