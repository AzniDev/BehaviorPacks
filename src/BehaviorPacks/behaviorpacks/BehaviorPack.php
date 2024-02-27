<?php

namespace BehaviorPacks\behaviorpacks;

use BehaviorPacks\loaders\BlockLoader;
use Symfony\Component\Filesystem\Path;

class BehaviorPack
{
    protected ?BlockLoader $blockLoader = null;

    public function __construct(protected string $name, string $path)
    {
        $blocksPath = Path::join($path, "blocks");
        if(file_exists($blocksPath)) $this->blockLoader = new BlockLoader($blocksPath);
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
}
