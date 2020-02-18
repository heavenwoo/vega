<?php


namespace Vega\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Vega\Entity\Tag;
use Vega\Repository\TagRepository;
use function Symfony\Component\String\u;

class TagArrayToStringTransformer implements DataTransformerInterface
{
    private $tags;

    public function __construct(TagRepository $tags)
    {
        $this->tags = $tags;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($tags): string
    {
        /** @var Tag[] $tags */
        return implode(',', $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string): array
    {
        if (null === $string || u($string)->isEmpty()) {
            return [];
        }

        $names = array_filter(array_unique(array_map('trim', u($string)->split(','))));

        $tags = $this->tags->findBy([
            'name' => $names,
        ]);

        $newNames = array_diff($names, $tags);

        foreach ($newNames as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $tags[] = $tag;
        }

        return $tags;
    }
}
