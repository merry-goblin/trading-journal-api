<?php

namespace App\Domain\Service\FrontPosition;

use App\Domain\Exception\NotFoundException\PositionNotFoundException;
use App\Domain\Exception\ValidationException\PositionValidationException;
use App\DTO\FrontApi\Position\FrontObservationOutput;
use App\DTO\FrontApi\Position\FrontPositionDetailOutput;
use App\DTO\FrontApi\Position\FrontPositionListOutput;
use App\DTO\FrontApi\Position\FrontScreenshotOutput;
use App\DTO\FrontApi\Position\FrontTagOutput;
use App\DTO\FrontApi\Position\PositionEnrichInput;
use App\Entity\Position;
use App\Repository\ChartObservation\ChartObservationRepositoryInterface;
use App\Repository\Position\PositionRepositoryInterface;
use App\Repository\Tag\TagRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FrontPositionService implements FrontPositionServiceInterface
{
    public function __construct(
        private PositionRepositoryInterface $positionRepository,
        private ChartObservationRepositoryInterface $observationRepository,
        private TagRepositoryInterface $tagRepository,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ) {}

    public function list(array $filters = [], int $page = 1, int $limit = 20): array
    {
        $positions = $this->positionRepository->findWithFilters($filters, $page, $limit);
        return array_map(fn(Position $p) => $this->toListOutput($p), $positions);
    }

    public function getDetail(int $id): FrontPositionDetailOutput
    {
        $position = $this->positionRepository->find($id);
        if (!$position) throw new PositionNotFoundException('Position not found');
        return $this->toDetailOutput($position);
    }

    public function enrich(int $id, PositionEnrichInput $input): FrontPositionDetailOutput
    {
        $violations = $this->validator->validate($input);
        if (count($violations) > 0) throw new PositionValidationException($violations);

        $position = $this->positionRepository->find($id);
        if (!$position) throw new PositionNotFoundException('Position not found');

        if ($input->hasPlanRespected)  $position->setPlanRespected($input->planRespected);
        if ($input->hasHigherTfBias)   $position->setHigherTfBias($input->higherTfBias);
        if ($input->hasEntryTfBias)    $position->setEntryTfBias($input->entryTfBias);
        if ($input->hasSetupQuality)   $position->setSetupQuality($input->setupQuality);
        if ($input->hasEmotionScore)   $position->setEmotionScore($input->emotionScore);
        if ($input->hasComment)        $position->setComment($input->comment);
        if ($input->hasIsBacktest)     $position->setIsBacktest($input->isBacktest ?? false);

        if ($input->hasTagIds && $input->tagIds !== null) {
            foreach ($position->getTags() as $tag) $position->removeTag($tag);
            foreach ($input->tagIds as $tagId) {
                $tag = $this->tagRepository->find($tagId);
                if ($tag) $position->addTag($tag);
            }
        }

        $this->em->flush();
        return $this->toDetailOutput($position);
    }

    private function toListOutput(Position $p): FrontPositionListOutput
    {
        $dto = new FrontPositionListOutput();
        $dto->id = $p->getId();
        $dto->assetSymbol = $p->getAsset()?->getSymbol();
        $dto->timeframeLabel = $p->getTimeframe()?->getLabel();
        $dto->direction = $p->getDirection();
        $dto->openedAt = $p->getOpenedAt()?->format('Y-m-d H:i:s');
        $dto->closedAt = $p->getClosedAt()?->format('Y-m-d H:i:s');
        $dto->entryPrice = $p->getEntryPrice();
        $dto->exitPrice = $p->getExitPrice();
        $dto->pnl = $p->getPnl();
        $dto->rr = $p->getRr();
        $dto->planRespected = $p->isPlanRespected();
        $dto->setupQuality = $p->getSetupQuality();
        $dto->isBacktest = $p->isBacktest();
        $dto->tagLabels = array_map(fn($t) => $t->getLabel(), $p->getTags()->toArray());
        return $dto;
    }

    private function toDetailOutput(Position $p): FrontPositionDetailOutput
    {
        $dto = new FrontPositionDetailOutput();
        $dto->id = $p->getId();
        $dto->assetId = $p->getAsset()?->getId();
        $dto->assetSymbol = $p->getAsset()?->getSymbol();
        $dto->timeframeId = $p->getTimeframe()?->getId();
        $dto->timeframeLabel = $p->getTimeframe()?->getLabel();
        $dto->originOrderId = $p->getOriginOrder()?->getId();
        $dto->openedAt = $p->getOpenedAt()?->format('Y-m-d H:i:s');
        $dto->closedAt = $p->getClosedAt()?->format('Y-m-d H:i:s');
        $dto->direction = $p->getDirection();
        $dto->entryPrice = $p->getEntryPrice();
        $dto->exitPrice = $p->getExitPrice();
        $dto->stopLoss = $p->getStopLoss();
        $dto->takeProfit = $p->getTakeProfit();
        $dto->volume = $p->getVolume();
        $dto->riskAmount = $p->getRiskAmount();
        $dto->pnl = $p->getPnl();
        $dto->pnlPercent = $p->getPnlPercent();
        $dto->rr = $p->getRr();
        $dto->comment = $p->getComment();
        $dto->planRespected = $p->isPlanRespected();
        $dto->higherTfBias = $p->getHigherTfBias();
        $dto->entryTfBias = $p->getEntryTfBias();
        $dto->setupQuality = $p->getSetupQuality();
        $dto->emotionScore = $p->getEmotionScore();
        $dto->isBacktest = $p->isBacktest();
        $dto->tags = array_map(function ($t) {
            $out = new FrontTagOutput();
            $out->id = $t->getId();
            $out->label = $t->getLabel();
            $out->type = $t->getType();
            return $out;
        }, $p->getTags()->toArray());

        $originOrderId = $p->getOriginOrder()?->getId();
        $observations = $this->observationRepository->findByPositionOrOrder($p->getId(), $originOrderId);
        $dto->observations = array_map(function ($obs) {
            $o = new FrontObservationOutput();
            $o->id = $obs->getId();
            $o->observedAt = $obs->getObservedAt()?->format('Y-m-d H:i:s');
            $o->trend = $obs->getTrend();
            $o->comment = $obs->getComment();
            $o->screenshots = array_map(function ($s) {
                $sc = new FrontScreenshotOutput();
                $sc->id = $s->getId();
                $sc->filePath = $s->getFilePath();
                $sc->createdAt = $s->getCreatedAt()?->format('Y-m-d H:i:s');
                $sc->source = $s->getSource();
                $sc->periodStart = $s->getPeriodStart()?->format('Y-m-d H:i:s');
                $sc->periodEnd = $s->getPeriodEnd()?->format('Y-m-d H:i:s');
                $sc->description = $s->getDescription();
                return $sc;
            }, $obs->getScreenshots()->toArray());
            return $o;
        }, $observations);

        return $dto;
    }
}
