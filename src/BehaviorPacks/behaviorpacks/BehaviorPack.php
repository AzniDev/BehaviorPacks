<?php

namespace BehaviorPacks\behaviorpacks;

use BehaviorPacks\loaders\BlockLoader;
use BehaviorPacks\loaders\RecipesLoader;
use Symfony\Component\Filesystem\Path;

class BehaviorPack
{
    protected ?BlockLoader $blockLoader = null;
    protected ?RecipesLoader $recipesLoader = null;

    public function __construct(protected string $name, string $path)
    {
        $blocksPath = Path::join($path, "blocks");
        if(file_exists($blocksPath)) $this->blockLoader = new BlockLoader($blocksPath);

        $recipesPath = Path::join($path, "recipes");
        if(file_exists($recipesPath)) $this->recipesLoader = new RecipesLoader($recipesPath);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return BlockLoader|null
     */
    public function getBlockLoader(): ?BlockLoader
    {
        return $this->blockLoader;
    }

    /**
     * @return RecipesLoader|null
     */
    public function getRecipesLoader(): ?RecipesLoader
    {
        return $this->recipesLoader;
    }
}
