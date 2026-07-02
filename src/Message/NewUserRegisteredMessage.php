<?php

namespace App\Message;

class NewUserRegisteredMessage
{
    public function __construct(
        private string $nom,
        private string $prenom,
        private string $identifiant,
        private string $type // 'visiteur' ou 'conferencier'
    ) {}

    public function getNom(): string
    {
        return $this->nom;
    }
    public function getPrenom(): string
    {
        return $this->prenom;
    }
    public function getIdentifiant(): string
    {
        return $this->identifiant;
    }
    public function getType(): string
    {
        return $this->type;
    }
}
