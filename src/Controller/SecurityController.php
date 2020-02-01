<?php

namespace Vega\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * @Route("/security")
 *
 * @package Vega\Controller
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     *
     * @param AuthenticationUtils $helper
     * @return Response
     */
    public function login(AuthenticationUtils $helper): Response
    {
        //$setting = $this->getSettings();
        $settings = $this->getParameter("settings");

        return $this->render("security/login.html.twig", [
            'setting' => $settings,
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
        return true;
    }
}
