<?php

namespace App\Utils;

use App\Entity\DepreciationGroup;
use Doctrine\Common\Collections\Collection;

class EnumerableSorter
{
    public function __construct()
    {
    }

    public function sortByCode(Collection $records): array
    {
        $recorsArr = $records->toArray();
        usort($recorsArr, function ($first, $second) {
            if ($first->getCode() > $second->getCode()) {
                return 1;
            }
            if ($first->getCode() < $second->getCode()) {
                return -1;
            };
            return 0;
        });

        return $recorsArr;
    }

    public function sortByCodeArr(array $records): array
    {
        usort($records, function ($first, $second) {
            if ($first->getCode() > $second->getCode()) {
                return 1;
            }
            if ($first->getCode() < $second->getCode()) {
                return -1;
            };
            return 0;
        });

        return $records;
    }

    public function sortGroupsByMethodAndNumber(array $groups): array
    {
        usort($groups, function (DepreciationGroup $first, DepreciationGroup $second) {
            if ($first->getMethod() > $second->getMethod()) {
                return 1;
            }
            if ($first->getMethod() < $second->getMethod()) {
                return -1;
            };
            if ($first->getGroup() > $second->getGroup()) {
                return 1;
            }
            if ($first->getGroup() < $second->getGroup()) {
                return -1;
            };

            return 0;
        });

        return $groups;
    }

    public function sortGroupsInArrayByMethodAndNumber(array $groups): array
    {
        uksort($groups, function (string $first, string $second) {
            $methodFirst = substr($first, 2);
            $methodSecond = substr($second, 2);
            $groupNumberFirst = substr($first, 0, 1);
            $groupNumberSecond = substr($second, 0, 1);

            if ($methodFirst > $methodSecond) {
                return 1;
            }
            if ($methodFirst < $methodSecond) {
                return -1;
            };
            if ($groupNumberFirst > $groupNumberSecond) {
                return 1;
            }
            if ($groupNumberFirst < $groupNumberSecond) {
                return -1;
            };

            return 0;
        });

        return $groups;
    }
}
