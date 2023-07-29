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
            'Majetek',
            $assetsItems
        );

        $dialsItems = $this->buildDialsItems();
        $menuItems[] = $this->createMenuSection(
            'Číselníky',
            $dialsItems
        );

        return $menuItems;
    }

    private function buildAssetsItems(): array
    {
        $items[] = $this->createMenuItem(
            'Majetek',
            $this->getPresenter()->lazyLink(':Admin:Dashboard:default'),
            [],
            $this->getCurrentLinkCallable()
        );

        return $items;
    }

    private function buildDialsItems(): array
    {
        $items[] = $this->createMenuItem(
            'Druhy majetku',
            $this->getPresenter()->lazyLink(':Admin:Dials:assetTypes'),
            [],
            $this->getCurrentLinkCallable()
        );
        $items[] = $this->createMenuItem(
            'Kategorie',
            $this->getPresenter()->lazyLink(':Admin:Dials:categories'),
            [],
            $this->getCurrentLinkCallable()
        );
        $items[] = $this->createMenuItem(
            'Odpisové skupiny',
            $this->getPresenter()->lazyLink(':Admin:Dials:depreciationGroups'),
            [],
            $this->getCurrentLinkCallable()
        );
        $items[] = $this->createMenuItem(
            'Střediska',
            $this->getPresenter()->lazyLink(':Admin:Dials:locations'),
            [],
            $this->getCurrentLinkCallable()
        );
        $items[] = $this->createMenuItem(
            'Míst',
            $this->getPresenter()->lazyLink(':Admin:Dials:places'),
            [],
            $this->getCurrentLinkCallable()
        );
        $items[] = $this->createMenuItem(
            'Způsoby pořízení',
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
