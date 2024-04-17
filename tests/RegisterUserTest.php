<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $client->request('GET', '/inscription');

        $client->submitForm('Valider',[
            'register[email]' => 'julie@admin.fr',
            'register[password][first]' => '123456',
            'register[password][second]' => '123456',
            'register[firstname]' => 'Julie',
            'register[lastname]' => 'Doe'
        ]);

        $this->assertResponseRedirects('/connexion');
        $client->followRedirect();

        $this->assertSelectorExists('div:contains("Votre compte a bien été créer")');
    }
}
