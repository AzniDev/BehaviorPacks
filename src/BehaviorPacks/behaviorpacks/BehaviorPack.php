<?php

namespace BehaviorPacks\behaviorpacks;

use BehaviorPacks\behaviorpacks\version\BehaviorVersion;
use BehaviorPacks\loaders\BlockLoader;
use BehaviorPacks\loaders\RecipesLoader;
use Symfony\Component\Filesystem\Path;

class BehaviorPack
{
    protected ?BlockLoader $blockLoader = null;
    protected ?RecipesLoader $recipesLoader = null;
    /**
     * @var BehaviorVersion[]
     */
    protected array $version = [];

    public function __construct(protected string $name, string $path)
    {
        $blocksPath = Path::join($path, "blocks");
        if(file_exists($blocksPath)) $this->blockLoader = new BlockLoader($this, $blocksPath);

        $recipesPath = Path::join($path, "recipes");
        if(file_exists($recipesPath)) $this->recipesLoader = new RecipesLoader($this, $recipesPath);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return BehaviorVersion[]
     */
    public function getVersions(): array
    {
        return $this->version;
    }

    /**
     * @param BehaviorVersion $version
     * @return void
     */
    public function addVersion(BehaviorVersion $version): void
    {
        $this->version[] = $version;
    }

    /**
     * @param BehaviorVersion[] $versions
     * @return void
     */
    public function setVersions(array $versions): void
    {
        $this->version = $versions;
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
