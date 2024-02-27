<?php

namespace BehaviorPacks\loaders;

use BehaviorPacks\utils\PathScanner;
use InvalidArgumentException;
use pocketmine\crafting\ExactRecipeIngredient;
use pocketmine\crafting\ShapedRecipe;
use pocketmine\item\StringToItemParser;
use pocketmine\Server;
use pocketmine\utils\Config;

class RecipesLoader
{
    public function __construct(protected string $path)
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

        $recipeShaped = $config->get("minecraft:recipe_shaped");
        if(!is_array($recipeShaped)) throw new InvalidArgumentException("Invalid minecraft:recipe_shaped");

        $tags = $recipeShaped["tags"] ?? null;
        if(!is_array($tags)) throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> tags");
        if(!in_array("crafting_table", $tags)) throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> tags");

        $pattern = $recipeShaped["pattern"] ?? null;
        if(!is_array($pattern)) throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> pattern");

        $key = $recipeShaped["key"] ?? null;
        if(!is_array($key)) throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> key");

        $result = $recipeShaped["result"] ?? null;
        if(!is_array($result)) throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> result");
        if(!is_string($result["item"] ?? null)) throw new InvalidArgumentException("Invalid minecraft:recipe_shaped -> result -> item");

        $resultItem = StringToItemParser::getInstance()->parse($result["item"])->setCount(intval($result["count"] ?? 1));
        $input = [];
        foreach ($key as $i => $item) $input[$i] = new ExactRecipeIngredient(StringToItemParser::getInstance()->parse($item));

        Server::getInstance()->getCraftingManager()->registerShapedRecipe(new ShapedRecipe($pattern, $input, [$resultItem]));
    }
}
