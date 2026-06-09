<?php
namespace App\Domain\Service\Tag;
use App\DTO\FrontApi\Tag\TagInput;
use App\Entity\Tag;
interface TagServiceInterface
{
    public function list(): array;
    public function get(int $id): Tag;
    public function create(TagInput $input): Tag;
}
