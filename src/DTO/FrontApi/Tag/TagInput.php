<?php
namespace App\DTO\FrontApi\Tag;
use Symfony\Component\Validator\Constraints as Assert;
class TagInput
{
    #[Assert\NotBlank]
    public string $label;

    #[Assert\NotBlank]
    public string $type;

    public string $description = '';
}
