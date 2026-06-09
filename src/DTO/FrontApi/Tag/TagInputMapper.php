<?php
namespace App\DTO\FrontApi\Tag;
use App\DTO\AbstractMapper;
class TagInputMapper extends AbstractMapper implements TagInputMapperInterface
{
    public function fromArray(array $data): TagInput
    {
        $dto = new TagInput();
        $dto->label = $this->stringOrEmpty($data['label'] ?? null);
        $dto->type = $this->stringOrEmpty($data['type'] ?? null);
        $dto->description = $this->stringOrEmpty($data['description'] ?? '');
        return $dto;
    }
}
