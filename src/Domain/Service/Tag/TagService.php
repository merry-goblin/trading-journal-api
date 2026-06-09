<?php
namespace App\Domain\Service\Tag;
use App\Domain\Exception\NotFoundException\TagNotFoundException;
use App\Domain\Exception\ValidationException\TagValidationException;
use App\DTO\FrontApi\Tag\TagInput;
use App\Entity\Tag;
use App\Repository\Tag\TagRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
class TagService implements TagServiceInterface
{
    public function __construct(
        private TagRepositoryInterface $repository,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ) {}

    public function list(): array
    {
        return $this->repository->findAll();
    }

    public function get(int $id): Tag
    {
        $tag = $this->repository->find($id);
        if (!$tag) throw new TagNotFoundException('Tag not found');
        return $tag;
    }

    public function create(TagInput $input): Tag
    {
        $violations = $this->validator->validate($input);
        if (count($violations) > 0) throw new TagValidationException($violations);

        $tag = new Tag();
        $tag->setLabel($input->label);
        $tag->setType($input->type);
        $tag->setDescription($input->description);

        $this->em->persist($tag);
        $this->em->flush();
        return $tag;
    }
}
