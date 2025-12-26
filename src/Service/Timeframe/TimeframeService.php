<?php

namespace App\Service\Timeframe;

use App\Domain\Exception\NotFoundException\TimeframeNotFoundException;
use App\Domain\Exception\ValidationException\TimeframeValidationException;
use App\DTO\Timeframe\TimeframeInput;
use App\Entity\Timeframe;
use App\Repository\TimeframeRepositoryInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TimeframeService implements TimeframeServiceInterface
{
    public function __construct(
        private TimeframeRepositoryInterface $repository,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ) {}

    public function list(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @throws TimeframeNotFoundException
     */
    public function get(int $id): Timeframe
    {
        $timeframe = $this->repository->find($id);
        if (null === $timeframe) {
            throw new TimeframeNotFoundException('Timeframe not found');
        }
        return $timeframe;
    }

    /**
     * @throws TimeframeNotFoundException
     */
    public function getByLabel(string $label): Timeframe
    {
        $timeframe = $this->repository->findOneBy(['label' => $label]);
        if (null === $timeframe) {
            throw new TimeframeNotFoundException('Timeframe not found');
        }
        return $timeframe;
    }

    /**
     * @throws LabelAlreadyExistsException
     */
    public function create(TimeframeInput $input): Timeframe
    {
        // Validation
        $violations = $this->validator->validate($input);
        if (count($violations) > 0) {
            throw new TimeframeValidationException($violations);
        }

        $timeframe = new Timeframe();
        $timeframe->setLabel($input->label);
        $timeframe->setSeconds($input->seconds);

        try {
            $this->em->persist($timeframe);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new LabelAlreadyExistsException($input->label.' timeframe already exists');
        }

        return $timeframe;
    }
}
