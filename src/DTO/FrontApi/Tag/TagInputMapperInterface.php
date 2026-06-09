<?php
namespace App\DTO\FrontApi\Tag;
interface TagInputMapperInterface
{
    public function fromArray(array $data): TagInput;
}
