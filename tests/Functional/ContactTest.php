<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ContactTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Contact form');

        //Recup du formulaire
        $submitButton = $crawler->selectButton('Send my message');
        $form = $submitButton->form();

        $form["contact[fullName]"] = "John Doe";
        $form["contact[Email]"] = "JDoe@hotmail.fr";
        $form["contact[Subject]"] = "test";
        $form["contact[Message]"] = "Test";


        //Soumission du form
        $client->submit($form);


        //Vérifier le statut HTTP (verifie par cette méthode que le code de la page générée est le 200(http found))
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        

        //Verif de l'envoi du mail
        $this->assertEmailCount(1);
        $client->followRedirect();


        //Verif la présence du message de succès
        $this->assertSelectorTextContains(
            'div.alert.alert-succes.mt-4',
            'Your message has successfully been sent'
        );
    }
}
