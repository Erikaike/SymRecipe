<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginTest extends WebTestCase
{
    public function testIfLoginIsSuccesfull(): void
    {
        $client = static::createClient();

        //Get route by url generator (grpace au service router)
        /**
         * @var UrlGeneratorInterface $urlGenerator
         */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));


        //Form
        $form = $crawler->filter('form[name=login]')->form([
            "_username" => "admin@ericipe.fr",
            "_password" => "password"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame('home.index');
    }

    public function testWrongMdp(): void
    {
        $client = static::createClient();

        //Get route by url generator (grpace au service router)
        /**
         * @var UrlGeneratorInterface $urlGenerator
         */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));


        //Form
        $form = $crawler->filter('form[name=login]')->form([
            "_username" => "admin@ericipe.fr",
            "_password" => "password_"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        
        $client->followRedirect();

        $this->assertRouteSame('security.login');

        //Apres la redirection la page présentée doit contenir une div de classe alert-danger et son contenu est invalid cred..
        $this->assertSelectorTextContains("div.alert-danger", "Invalid credentials");

    }
}
