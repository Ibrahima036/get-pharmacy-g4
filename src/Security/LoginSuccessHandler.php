<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\{Request, RedirectResponse, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ? Response
    {
        $user = $token->getUser();

        $url = $this->urlGenerator->generate('app_login');

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $url = 'app_dashboard_admin';
        }
        if (in_array('ROLE_PHARMACIST', $user->getRoles(), true)) {
            $url = 'app_dashboard_pharmacist';
        }
        if (in_array('ROLE_SELLER', $user->getRoles(), true)) {
            $url = 'app_dashboard_seller';
        }

        return new RedirectResponse($this->urlGenerator->generate($url));

    }
}
