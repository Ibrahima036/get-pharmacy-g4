<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\{Request, RedirectResponse, Response};
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private readonly RouterInterface $router) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ? Response
    {
        $roles = $token->getRoleNames();
        return new RedirectResponse($this->router->generate('app_product_index'));

    }
}
