<?php

namespace App\Controller\Admin;

use App\Entity\Blog\Post;
use App\Entity\Event;
use App\Entity\Job;
use App\Entity\News\Entry;
use App\Entity\News\Feed;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator->setController(JobCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Jobbsy');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Jobs', 'fas fa-list', Job::class);
        yield MenuItem::linkToCrud('Events', 'fas fa-calendar', Event::class);

        yield MenuItem::linkToCrud('Blog Posts', 'fas fa-file-text', Post::class);

        yield MenuItem::subMenu('News', 'fas fa-newspaper')->setSubItems([
            MenuItem::linkToCrud('Entries', 'fas fa-list', Entry::class),
            MenuItem::linkToCrud('Feeds', 'fas fa-rss', Feed::class),
        ]);

        yield MenuItem::section('Channels');
        yield MenuItem::linkToUrl('View website', 'fas fa-external-link', '/');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenu = parent::configureUserMenu($user);
        $userMenu->setMenuItems([]);

        return $userMenu;
    }
}
