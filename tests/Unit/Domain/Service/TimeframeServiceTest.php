<?php

namespace App\Tests\Unit\Domain\Service;

use App\Domain\Exception\NotFoundException\TimeframeNotFoundException;
use PHPUnit\Framework\TestCase;

use App\DTO\Timeframe\TimeframeInput;
use App\Entity\Timeframe;

use App\Domain\Service\Timeframe\TimeframeService;
use App\Repository\Timeframe\TimeframeRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Domain\Service\Timeframe\LabelAlreadyExistsException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Driver\Exception as DriverException;
use App\Domain\Exception\ValidationException\TimeframeValidationException;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TimeframeServiceTest extends TestCase
{
    /* get method */

    public function testGetOneTimeframeById(): void
    {
        // Mock data
        $expected = $this->createTimeframe(1, 'M1', 60);

        // Dependency injections
        $timeframeRepository = $this->createMock(TimeframeRepositoryInterface::class);
        $timeframeRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($expected)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Start test
        $timeframeService = new TimeframeService($timeframeRepository, $em, $validator);
        $timeframe = $timeframeService->get(1);

        // Assertions
        $this->assertInstanceOf(Timeframe::class, $timeframe);
        $this->assertSame($expected, $timeframe);
    }

    public function testGetOneTimeframeByIdNotFound(): void
    {
        // Mock data
        $expected = null;

        // Dependency injections
        $timeframeRepository = $this->createMock(TimeframeRepositoryInterface::class);
        $timeframeRepository->expects(self::once())
            ->method('find')
            ->with(2)
            ->willReturn($expected)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Assertions
        $this->expectException(TimeframeNotFoundException::class);
        $this->expectExceptionMessage('Timeframe not found');

        // Start test
        $timeframeService = new TimeframeService($timeframeRepository, $em, $validator);
        $timeframe = $timeframeService->get(2);
    }

    /* getByLabel method */

    public function testGetByLabelOneTimeframe(): void
    {
        // Mock data
        $expected = $this->createTimeframe(1, 'M1', 60);

        // Dependency injections
        $timeframeRepository = $this->createMock(TimeframeRepositoryInterface::class);
        $timeframeRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['label' => 'M1'])
            ->willReturn($expected)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Start test
        $timeframeService = new TimeframeService($timeframeRepository, $em, $validator);
        $timeframe = $timeframeService->getByLabel('M1');

        // Assertions
        $this->assertInstanceOf(Timeframe::class, $timeframe);
        $this->assertSame($expected, $timeframe);
    }

    public function testGetByLabelWithLabelNotFound(): void
    {
        // Mock data
        $expected = null;

        // Dependency injections
        $timeframeRepository = $this->createMock(TimeframeRepositoryInterface::class);
        $timeframeRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['label' => 'Y2B'])
            ->willReturn($expected)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Assertions
        $this->expectException(TimeframeNotFoundException::class);
        $this->expectExceptionMessage('Timeframe not found');

        // Start test
        $timeframeService = new TimeframeService($timeframeRepository, $em, $validator);
        $timeframe = $timeframeService->getByLabel('Y2B');
    }

    /* list method */

    public function testGetAllTimeframes(): void
    {
        // Mock data
        $expectedList = [
            $this->createTimeframe(1, 'M1', 60),
            $this->createTimeframe(1, 'M5', 300),
        ];

        // Dependency injections
        $timeframeRepository = $this->createMock(TimeframeRepositoryInterface::class);
        $timeframeRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($expectedList)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Start test
        $timeframeService = new TimeframeService($timeframeRepository, $em, $validator);
        $timeframeList = $timeframeService->list();

        // Assertions
        $this->assertIsArray($timeframeList);
        $this->assertCount(count($expectedList), $timeframeList);
        $this->assertContainsOnlyInstancesOf(Timeframe::class, $timeframeList);
    }

    public function testGetAllTimeframesNoData(): void
    {
        // Mock data
        $expectedList = [];

        // Dependency injections
        $timeframeRepository = $this->createMock(TimeframeRepositoryInterface::class);
        $timeframeRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($expectedList)
        ;
        $em = $this->createStub(EntityManagerInterface::class);
        $validator = $this->createStub(ValidatorInterface::class);

        // Start test
        $timeframeService = new TimeframeService($timeframeRepository, $em, $validator);
        $timeframeList = $timeframeService->list();

        // Assertions
        $this->assertIsArray($timeframeList);
        $this->assertCount(count($expectedList), $timeframeList);
    }

    /* create method */

    public function testCreateTimeframe(): void
    {
        // Mock data
        $input = $this->createTimeframeInput('M1', 60);

        // Dependency injection
        $timeframeRepository = $this->createStub(TimeframeRepositoryInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())
            ->method('persist')
        ;
        $em->expects(self::once())
            ->method('flush')
        ;
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList())
        ;

        // Start test
        $timeframeService = new TimeframeService($timeframeRepository, $em, $validator);
        $timeframe = $timeframeService->create($input);

        // Assertions
        $this->assertIsObject($timeframe);
        $this->assertInstanceOf(Timeframe::class, $timeframe);
        $this->assertSame($input->label, $timeframe->getLabel());
        $this->assertSame($input->seconds, $timeframe->getSeconds());
    }

    public function testCreateTimeframeWithSymbolDuplication(): void
    {
        // Mock data
        $input = $this->createTimeframeInput('M1', 60);
        $exception = new UniqueConstraintViolationException(
            $this->createStub(DriverException::class),
            null
        );

        // Dependency injection
        $timeframeRepository = $this->createStub(TimeframeRepositoryInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())
            ->method('persist')
        ;
        $em->expects(self::once())
            ->method('flush')
            ->willThrowException($exception)
        ;
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList())
        ;

        // Assertions
        $this->expectException(LabelAlreadyExistsException::class);
        $this->expectExceptionMessage('M1 timeframe already exists');

        // Start test
        $timeframeService = new TimeframeService($timeframeRepository, $em, $validator);
        $timeframeService->create($input);
    }

    public function testCreateTimeframeWithInvalidPayloadThrowsException(): void
    {
        // Mock data
        $input = $this->createTimeframeInput('', 0);
        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'This value should not be blank.',
                messageTemplate: null,
                parameters: [],
                root: $input,
                propertyPath: 'label',
                invalidValue: '',
            ),
        ]);

        // Dependency injection
        $timeframeRepository = $this->createStub(TimeframeRepositoryInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())
            ->method('persist')
        ;
        $em->expects(self::never())
            ->method('flush')
        ;
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn($violations)
        ;

        // Assertions
        $this->expectException(TimeframeValidationException::class);

        // Start test
        $timeframeService = new TimeframeService($timeframeRepository, $em, $validator);
        $timeframeService->create($input);
    }

    /* private methods */

    private function createTimeframe(
        int $id,
        string $label,
        int $seconds
    ): Timeframe {
        $timeframe = new Timeframe();
        $timeframe->setId($id);
        $timeframe->setLabel($label);
        $timeframe->setSeconds($seconds);

        return $timeframe;
    }

    private function createTimeframeInput(
        string $label,
        int $seconds
    ): TimeframeInput {
        $timeframeInput = new TimeframeInput();
        $timeframeInput->label = $label;
        $timeframeInput->seconds = $seconds;

        return $timeframeInput;
    }
}
