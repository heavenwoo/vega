<?php

namespace Vega\Controller\Admin;

use Vega\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DashboardController
 *
 * @Route("/admin")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @package Vega\Controller\Admin
 */
class DashboardController extends Controller
{
    /**
     * @Route("", name="admin_dashboard")
     */
    public function dashboard()
    {
        return new Response('OK');
    }
}
