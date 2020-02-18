<?php

namespace Vega\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Vega\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DashboardController
 *
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 *
 * @package Vega\Controller\Admin
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="admin_dashboard")
     */
    public function dashboard(Request $request): Response
    {
        return $this->json($request);
    }
}
