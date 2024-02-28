<?php

namespace BehaviorPacks\behaviorpacks\version;

use pocketmine\utils\Config;

abstract class BehaviorVersion
{
    public const VERSION = [
        "1.12" => BehaviorPack_1_12::class,
        "1.16.0" => BehaviorPack_1_16::class,
        "1.16.100" => BehaviorPack_1_16::class,
        "1.17" => BehaviorPack_1_16::class,
        "1.18" => BehaviorPack_1_16::class,
        "1.19" => BehaviorPack_1_19_40::class,
        "1.20" => BehaviorPack_1_19_40::class
    ];

    /**
     * @param string $version
     * @return string|null
     */
    public static function getClassVersion(string $version): ?string
    {
        foreach(self::VERSION as $supportVersion => $class){
            if(strstr($version, $supportVersion) !== false){
                return $class;
            }
        }
        return null;
    }

    /**
     * @param Config $config
     * @return void
     */
    abstract public function parseBlock(Config $config): void;
    abstract public function parseRecipe(Config $config): void;
}
