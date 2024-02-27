<?php

namespace BehaviorPacks\loaders;

use BehaviorPacks\BehaviorPacks;
use BehaviorPacks\block\PermutableBlock;
use BehaviorPacks\utils\PathScanner;
use customiesdevs\customies\block\CustomiesBlockFactory;
use customiesdevs\customies\block\Material;
use customiesdevs\customies\block\Model;
use customiesdevs\customies\item\CreativeInventoryInfo;
use InvalidArgumentException;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;

class BlockLoader
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

        $blockFlag = $config->get("minecraft:block");
        if(!is_array($blockFlag)) throw new InvalidArgumentException("Invalid minecraft:block");

        $description = $blockFlag["description"] ?? null;
        if(!is_array($description)) throw new InvalidArgumentException("Invalid minecraft:block -> description");

        $identifier = $description["identifier"] ?? null;
        if(!is_string($identifier)) throw new InvalidArgumentException("Invalid minecraft:block -> description -> identifier");

        $components = $blockFlag["components"] ?? null;
        if(is_array($components)) {
            if(isset($components["minecraft:destroy_time"])) {
                $destroyTime = $components["minecraft:destroy_time"];
                if(is_float($destroyTime) || is_int($destroyTime)) {
                    $breakInfo = new BlockBreakInfo($components["minecraft:destroy_time"] / 20);
                }
            }

            $materialInstance = $components["minecraft:material_instances"] ?? null;
            if(!is_array($materialInstance))  return;//throw new InvalidArgumentException("Invalid minecraft:block -> components -> minecraft:material_instances");
            $materials = [];
            foreach ($materialInstance as $key => $value) {
                if(!in_array($key, [
                    Material::TARGET_ALL,
                    Material::TARGET_SIDES,
                    Material::TARGET_UP,
                    Material::TARGET_DOWN,
                    Material::TARGET_NORTH,
                    Material::TARGET_EAST,
                    Material::TARGET_SOUTH,
                    Material::TARGET_WEST,
                    "bottom" => "bottom" // WHY??
                ]))  return;//throw new InvalidArgumentException("Invalid minecraft:block -> components -> minecraft:material_instances -> {$key}");

                $renderMethod = $value["render_method"] ?? Material::RENDER_METHOD_BLEND;

                $texture = $value["texture"] ?? null;
                if(!is_string($texture)) return;//throw new InvalidArgumentException("Invalid minecraft:block -> components -> minecraft:material_instances -> '*' -> texture");

                $materials[] = new Material(Material::TARGET_ALL, $texture, $renderMethod);
            }

            $geometry = $components["minecraft:geometry"] ?? null;
            if(is_string($geometry)) {
                $model = new Model($materials, $geometry);

                $entityCollision = $components["minecraft:entity_collision"] ?? null;
                if(is_array($entityCollision)) {
                    $origin = $entityCollision["origin"] ?? [-8, 0, -8];
                    $size = $entityCollision["size"] ?? [16, 16, 16];

                    if(count($origin) !== 3) throw new InvalidArgumentException("Invalid minecraft:block -> components -> minecraft:entity_collision -> origin");
                    if(count($size) !== 3) throw new InvalidArgumentException("Invalid minecraft:block -> components -> minecraft:entity_collision -> size");

                    $model->setCollisionBox(true, new Vector3($origin[0], $origin[1], $origin[2]), new Vector3($size[0], $size[1], $size[2]));
                } else $model->setCollisionBox(true, new Vector3(-8, 0, -8), new Vector3(16, 16, 16));

                $pickCollision = $components["minecraft:pick_collision"] ?? null;
                if(is_array($pickCollision)) {
                    $origin = $pickCollision["origin"] ?? [-8, 0, -8];
                    $size = $pickCollision["size"] ?? [16, 16, 16];

                    if(count($origin) !== 3) throw new InvalidArgumentException("Invalid minecraft:block -> components -> minecraft:pick_collision -> origin");
                    if(count($size) !== 3) throw new InvalidArgumentException("Invalid minecraft:block -> components -> minecraft:pick_collision -> size");

                    $model->setSelectionBox(true, new Vector3($origin[0], $origin[1], $origin[2]), new Vector3($size[0], $size[1], $size[2]));
                } else $model->setSelectionBox(true, new Vector3(-8, 0, -8), new Vector3(16, 16, 16));
            }
        }

        $breakInfo ??= BlockBreakInfo::instant();
        $model ??= null;

        $block = static fn() => (new PermutableBlock(new BlockIdentifier(BlockTypeIds::newId()), "Unknown", new BlockTypeInfo($breakInfo ?? BlockBreakInfo::instant())));
        CustomiesBlockFactory::getInstance()->registerBlock($block, $identifier, $model, new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_NATURE, CreativeInventoryInfo::GROUP_MONSTER_STONE_EGG));
    }
}
