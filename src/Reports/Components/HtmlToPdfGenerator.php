<?php
declare(strict_types=1);

namespace App\Reports\Components;

use App\Majetek\ORM\AssetTypeRepository;
use App\Majetek\ORM\CategoryRepository;
use App\Majetek\ORM\DepreciationGroupRepository;
use App\Majetek\ORM\PlaceRepository;

class HtmlToPdfGenerator
{
    private PlaceRepository $placeRepository;
    private CategoryRepository $categoryRepository;
    private AssetTypeRepository $assetTypeRepository;
    private DepreciationGroupRepository $depreciationGroupRepository;

    public function __construct(
        PlaceRepository $placeRepository,
        CategoryRepository $categoryRepository,
        AssetTypeRepository $assetTypeRepository,
        DepreciationGroupRepository $depreciationGroupRepository,
    )
    {
        $this->placeRepository = $placeRepository;
        $this->categoryRepository = $categoryRepository;
        $this->assetTypeRepository = $assetTypeRepository;
        $this->depreciationGroupRepository = $depreciationGroupRepository;
    }

    public function generateHtmlHead(): string
    {
        $data = '<html lang="cs-CZ"><head><meta http-equiv="Content-Type" charset="UTF-8"/><title></title>';
        $css = '<style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 12px;}
                .table > * > * > * { padding: .2rem .2rem; border-bottom-width: 1px; border-color: rgb(222, 226, 230);} 
                .table > thead { vertical-align: bottom; }
                tbody, td, tfoot, th, thead, tr { border-width: 0; border-style: solid; border-color: inherit;}
                .table-bordered > * > * > * { border-width: 0 1px }
                table { border-collapse: collapse; border: 1px solid black; width: 100%}
                ';
        $css .= '</style>';
        $data .= $css;
        $data .= '</head><body>';
        return $data;
    }

    public function writeAssetTypeNames(?array $array, string $label): string
    {
        if ($array && count($array) > 0) {
            $line = $label;
            $counter = 0;
            foreach ($array as $item) {
                $counter++;
                $type = $this->assetTypeRepository->find($item);
                if ($counter === 1) {
                    $line .= $type->getName();
                    continue;
                }
                $line .= ', ' . $type->getName();
            }

            return $this->addNewLine($line);
        }

        return '';
    }

    public function writeCategoryNames(?array $array, string $label): string
    {
        if ($array && count($array) > 0) {
            $line = $label;
            $counter = 0;
            foreach ($array as $item) {
                $counter++;
                $category = $this->categoryRepository->find($item);
                if ($counter === 1) {
                    $line .= $category->getName();
                    continue;
                }
                $line .= ', ' . $category->getName();
            }

            return $this->addNewLine($line);
        }

        return '';
    }

    public function writePlaceNames(?array $array, string $label): string
    {
        if ($array && count($array) > 0) {
            $line = $label;
            $counter = 0;
            foreach ($array as $item) {
                $counter++;
                $place = $this->placeRepository->find($item);
                if ($counter === 1) {
                    $line .= $place->getName();
                    continue;
                }
                $line .= ', ' . $place->getName();
            }

            return $this->addNewLine($line);
        }

        return '';
    }

    public function writeDepreciationGroupNames(?array $array, string $label): string
    {
        if ($array && count($array) > 0) {
            $line = $label;
            $counter = 0;
            foreach ($array as $item) {
                $counter++;
                $place = $this->depreciationGroupRepository->find($item);
                if ($counter === 1) {
                    $line .= $place->getFullName();
                    continue;
                }
                $line .= ', ' . $place->getFullName();
            }

            return $this->addNewLine($line);
        }

        return '';
    }

    protected function addNewLine(string $html): string
    {
        return $html . '<br>';
    }
}
