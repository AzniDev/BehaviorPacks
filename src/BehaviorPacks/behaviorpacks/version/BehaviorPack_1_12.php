<?php

namespace BehaviorPacks\behaviorpacks\version;

use pocketmine\crafting\ExactRecipeIngredient;
use pocketmine\crafting\ShapedRecipe;
use pocketmine\item\StringToItemParser;
use pocketmine\Server;
use pocketmine\utils\Config;

class BehaviorPack_1_12 extends BehaviorVersion
{
    /**
     * @param Config $config
     * @return void
     */
    public function parseBlock(Config $config): void { }

    /**
     * @param Config $config
     * @return void
     */
    public function parseRecipe(Config $config): void
    {
        $recipeShaped = $config->get("minecraft:recipe_shaped");
        if(!is_array($recipeShaped)) return; //throw new InvalidArgumentException("Invalid minecraft:recipe_shaped");

        $tags = $recipeShaped["tags"] ?? null;
        if(!is_array($tags)) return; //throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> tags");
        if(!in_array("crafting_table", $tags)) return; //throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> tags");

        $pattern = $recipeShaped["pattern"] ?? null;
        if(!is_array($pattern)) return; //throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> pattern");

        $key = $recipeShaped["key"] ?? null;
        if(!is_array($key)) return; //throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> key");

        $result = $recipeShaped["result"] ?? null;
        if(!is_array($result)) return; //throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> result");
        if(!is_string($result["item"] ?? null)) return; //throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> result -> item");

        $resultItem = StringToItemParser::getInstance()->parse($result["item"])->setCount(intval($result["count"] ?? 1));
        $input = [];
        foreach ($key as $i => $item) $input[$i] = new ExactRecipeIngredient(StringToItemParser::getInstance()->parse($item));

        Server::getInstance()->getCraftingManager()->registerShapedRecipe(new ShapedRecipe($pattern, $input, [$resultItem]));
    }
}
