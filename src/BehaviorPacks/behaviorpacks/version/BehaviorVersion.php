<?php

namespace BehaviorPacks\behaviorpacks\version;

use pocketmine\utils\Config;

abstract class BehaviorVersion
{
    public const VERSION = [
        "1.12", BehaviorPack_1_12::class,
        "1.16.0", "1.16.100", "1.17.0" => BehaviorPack_1_16::class,
    ];

    /**
     * @param Config $config
     * @return void
     */
    abstract public function parseBlock(Config $config): void;
    abstract public function parseRecipe(Config $config): void;
}
