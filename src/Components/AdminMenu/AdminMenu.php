<?php

namespace App\Components\AdminMenu;

use App\Entity\AccountingEntity;
use Nette\Application\UI\Control;
use Nette\Application\UI\Link;

class AdminMenu extends Control
{

    public function __construct()
    {
    }

    public function render(?AccountingEntity $accountingEntity)
    {
        $sections = $this->buildMenuItems();
        $this->template->currentEntity = $accountingEntity;
        $this->template->sections = $sections;

        $profileLink = $this->getPresenter()->lazyLink(':Admin:Profile:default');
        $entitiesLink = $this->getPresenter()->lazyLink(':Admin:EntitiesOverview:default');
        $signOutLink = $this->getPresenter()->lazyLink(':Home:signOut');
        $this->template->profileLink = $profileLink;
        $this->template->isProfileLinkActive = $this->getPresenter()->isLinkCurrent($profileLink->getDestination(), $profileLink->getParameters());
        $this->template->entitiesLink = [$entitiesLink, $this->getPresenter()->isLinkCurrent($entitiesLink->getDestination(), $entitiesLink->getParameters())];
        $this->template->signOutLink = $signOutLink;

        $this->template->setFile(__DIR__ . '/templates/menu.latte');
        $this->template->render();
    }

    private function createMenuSection(string $text, array $items): MenuSection
    {
        return new MenuSection($text, $items);
    }

    private function createMenuItem(string $text, ?Link $link, array $children, ?callable $isLinkCurrent): MenuItem
    {
        return new MenuItem(
            $text,
            $link,
            $children,
            $isLinkCurrent,
        );
    }

    private function buildMenuItems(): array
    {
        $menuItems = [];

        $assetsItems = $this->buildAssetsItems();
        $menuItems[] = $this->createMenuSection(
            'Assets',
            $assetsItems
        );

        $depreciationsItems = $this->buildDepreciationsItems();
        $menuItems[] = $this->createMenuSection(
            'Depreciations',
            $depreciationsItems
        );

        $dialsItems = $this->buildDialsItems();
        $menuItems[] = $this->createMenuSection(
            'Enums',
            $dialsItems
        );

        return $menuItems;
    }

    private function buildAssetsItems(): array
    {
        $items[] = $this->createMenuItem(
            'Overview',
            $this->getPresenter()->lazyLink(':Admin:Assets:default'),
            [],
            $this->getCurrentLinkCallable()
        );

        return $items;
    }

    private function buildDepreciationsItems(): array
    {
        $items[] = $this->createMenuItem(
            'Depreciations',
            $this->getPresenter()->lazyLink(':Admin:Depreciations:default'),
            [],
            $this->getCurrentLinkCallable()
        );
        $items[] = $this->createMenuItem(
            'Execute depreciations',
            $this->getPresenter()->lazyLink(':Admin:ExecuteDepreciations:default'),
            [],
            $this->getCurrentLinkCallable()
        );

        return $items;
    }

    private function buildDialsItems(): array
    {
        $items[] = $this->createMenuItem(
            'Asset types',
            $this->getPresenter()->lazyLink(':Admin:Dials:assetTypes'),
            [],
            $this->getCurrentLinkCallable()
        );
        $items[] = $this->createMenuItem(
            'Categories',
            $this->getPresenter()->lazyLink(':Admin:Dials:categories'),
            [],
            $this->getCurrentLinkCallable()
        );
        $items[] = $this->createMenuItem(
            'Depreciation groups',
            $this->getPresenter()->lazyLink(':Admin:Dials:depreciationGroups'),
            [],
            $this->getCurrentLinkCallable()
        );
        $items[] = $this->createMenuItem(
            'Locations',
            $this->getPresenter()->lazyLink(':Admin:Dials:locations'),
            [],
            $this->getCurrentLinkCallable()
        );
        $items[] = $this->createMenuItem(
            'Places',
            $this->getPresenter()->lazyLink(':Admin:Dials:places'),
            [],
            $this->getCurrentLinkCallable()
        );
        $items[] = $this->createMenuItem(
            'Acquisition/disposal methods',
            $this->getPresenter()->lazyLink(':Admin:Dials:acquisitions'),
            [],
            $this->getCurrentLinkCallable()
        );

        return $items;
    }

    private function getCurrentLinkCallable(): callable
    {
        return function (Link $link) {
            return $this->getPresenter()->isLinkCurrent($link->getDestination(), $link->getParameters());
        };
    }
}
