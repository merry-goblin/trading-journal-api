<?php

namespace App\Domain\Service\ChartObservation;

use App\DTO\ChartObservation\ChartObservationInput;
use App\Entity\ChartObservation;

interface ChartObservationServiceInterface
{
    public function list(): array;
    public function get(int $id): ChartObservation;
    public function create(ChartObservationInput $input): ChartObservation;
    /**
     * Mise a jour partielle : seuls les champs presents dans $data sont appliques.
     * Champs supportes : trend (string|null), comment (string|null)
     */
    public function update(int $id, array $data): ChartObservation;
    /**
     * Supprime l'observation, ses screenshots en base (cascade Doctrine)
     * et les fichiers physiques sur disque.
     */
    public function delete(int $id): void;
}
