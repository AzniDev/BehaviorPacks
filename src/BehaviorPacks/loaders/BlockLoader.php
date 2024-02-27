<?php

namespace BehaviorPacks\loaders;

use BehaviorPacks\BehaviorPacks;
use BehaviorPacks\behaviorpacks\BehaviorPack;
use BehaviorPacks\behaviorpacks\version\BehaviorVersion;
use BehaviorPacks\utils\PathScanner;
use InvalidArgumentException;
use pocketmine\utils\Config;

class BlockLoader
{
    public function __construct(protected BehaviorPack $behaviorPack, protected string $path)
    {
        $this->load();
    }

    /**
     * @return void
     */
    public function load(): void
    {
        $files = PathScanner::scanDirectoryToConfig($this->path);
        foreach ($files as $config) {
            $this->parse($config);
        }
    }

    /**
     * @param Config $config
     * @return void
     */
    public function parse(Config $config): void
    {
        $version = $config->get("format_version", null);
        if(!is_string($version)) throw new InvalidArgumentException("Invalid format version ($version)");

        if(!isset(BehaviorVersion::VERSION[$version])) throw new InvalidArgumentException("Invalid version ($version)");
        $class = BehaviorVersion::VERSION[$version];

        /** @type BehaviorVersion $behaviorVersion */
        $behaviorVersion = new $class();
        $this->behaviorPack->addVersion($behaviorVersion);
        $behaviorVersion->parseBlock($config);
    }
}
