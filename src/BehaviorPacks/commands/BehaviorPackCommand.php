<?php

namespace BehaviorPacks\commands;

use BehaviorPacks\BehaviorPacks;
use BehaviorPacks\behaviorpacks\BehaviorPack;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\permission\DefaultPermissions;
use pocketmine\utils\TextFormat;

class BehaviorPackCommand extends Command
{
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission(DefaultPermissions::ROOT_USER);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $list = array_map(function(BehaviorPack $behaviorPack) : string{
            return TextFormat::GREEN . $behaviorPack->getName();
        }, BehaviorPacks::getInstance()->getAddons());
        sort($list, SORT_STRING);

        $sender->sendMessage("BehaviorPacks (" . count($list) . ")" . TextFormat::GRAY . ": " . implode(TextFormat::RESET . ", ", $list));
    }
}
