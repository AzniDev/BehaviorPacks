<?php

namespace BehaviorPacks;

use BehaviorPacks\behaviorpacks\BehaviorPack;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Symfony\Component\Filesystem\Path;

class BehaviorPacks extends PluginBase
{
    use SingletonTrait;

    /**
     * @var BehaviorPack[]
     */
    protected array $addons = [];

    public function onLoad(): void
    {
        self::setInstance($this);
        foreach (scandir($this->getDataFolder(),0) as $file) {
            if ($file === ".." || $file === '.') continue;
            if (is_dir($realpath = Path::join($this->getDataFolder(), $file))) {
                $this->addons[strtolower($file)] = new BehaviorPack($file, $realpath);
            }
        }
    }
}
