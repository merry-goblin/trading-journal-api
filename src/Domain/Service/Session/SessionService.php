<?php

namespace App\Domain\Service\Session;

use App\Entity\DailySession;
use App\Repository\Asset\AssetRepositoryInterface;
use App\Repository\DailySession\DailySessionRepositoryInterface;
use DateTimeImmutable;

class SessionService implements SessionServiceInterface
{
    public function __construct(
        private DailySessionRepositoryInterface $repository,
        private AssetRepositoryInterface $assetRepository
    ) {}

    public function getOrEmpty(DateTimeImmutable $date): array
    {
        $session = $this->repository->findByDate($date);
        return $this->toOutput($date, $session);
    }

    public function update(DateTimeImmutable $date, array $data): array
    {
        $session = $this->repository->findByDate($date);
        if (!$session) $session = new DailySession($date);

        // Mise a jour partielle — seuls les champs presents dans $data sont appliques
        if (array_key_exists('preBias', $data))
            $session->setPreBias($data['preBias']);

        if (array_key_exists('preKeyLevels', $data))
            $session->setPreKeyLevels($data['preKeyLevels'] ?: null);

        if (array_key_exists('preAnalysis', $data))
            $session->setPreAnalysis($data['preAnalysis'] ?: null);

        if (array_key_exists('intraNotes', $data))
            $session->setIntraNotes($data['intraNotes'] ?: null);

        if (array_key_exists('postReview', $data))
            $session->setPostReview($data['postReview'] ?: null);

        if (array_key_exists('postEmotionScore', $data))
            $session->setPostEmotionScore($data['postEmotionScore'] !== null
                ? (int) $data['postEmotionScore'] : null);

        if (array_key_exists('postDiscipline', $data))
            $session->setPostDiscipline($data['postDiscipline'] !== null
                ? (bool) $data['postDiscipline'] : null);

        if (array_key_exists('assetId', $data) && $data['assetId']) {
            $asset = $this->assetRepository->find((int) $data['assetId']);
            $session->setAsset($asset);
        }

        $session->touch();
        $this->repository->save($session);
        return $this->toOutput($date, $session);
    }

    private function toOutput(DateTimeImmutable $date, ?DailySession $session): array
    {
        if (!$session) {
            return [
                'date'             => $date->format('Y-m-d'),
                'exists'           => false,
                'assetId'          => null,
                'assetSymbol'      => null,
                'preBias'          => null,
                'preKeyLevels'     => null,
                'preAnalysis'      => null,
                'intraNotes'       => null,
                'postReview'       => null,
                'postEmotionScore' => null,
                'postDiscipline'   => null,
                'updatedAt'        => null,
            ];
        }

        return [
            'date'             => $session->getDate()->format('Y-m-d'),
            'exists'           => true,
            'assetId'          => $session->getAsset()?->getId(),
            'assetSymbol'      => $session->getAsset()?->getSymbol(),
            'preBias'          => $session->getPreBias(),
            'preKeyLevels'     => $session->getPreKeyLevels(),
            'preAnalysis'      => $session->getPreAnalysis(),
            'intraNotes'       => $session->getIntraNotes(),
            'postReview'       => $session->getPostReview(),
            'postEmotionScore' => $session->getPostEmotionScore(),
            'postDiscipline'   => $session->getPostDiscipline(),
            'updatedAt'        => $session->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
