<?php

namespace App\Tests;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected DocumentManager $dm;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        
        // CORRECTION : On utilise la classe directement (FQCN) pour récupérer le DocumentManager de manière fiable
        $this->dm = static::getContainer()->get(DocumentManager::class);
        
        // Vide la base de données MongoDB de test avant chaque exécution
        $this->dm->getSchemaManager()->dropDatabases();
        $this->dm->getSchemaManager()->createCollections();
    }


    protected function registerTestUser(string $email, string $password, string $name = 'Test User', array $roles = ['ROLE_USER']): void
    {
        // 1. Inscription classique via l'endpoint de ton API
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ])
        );

        // 2. Si on demande des rôles spécifiques (comme ROLE_ADMIN), on force en BDD MongoDB
        if (!empty($roles) && $roles !== ['ROLE_USER']) {
            // On récupère le DocumentManager de Doctrine
            $dm = self::getContainer()->get(\Doctrine\ODM\MongoDB\DocumentManager::class);
            
   !
            $user = $dm->getRepository(\App\Document\User::class)->findOneBy(['email' => $email]);
            
            if ($user) {
                $user->setRoles($roles);
                $dm->flush(); // On sauvegarde la modification en BDD de test
                $dm->clear(); // On nettoie la mémoire
            }
        }
    }

    protected function authenticateClient(string $email, string $password): void
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email, 'password' => $password])
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        
        // Une petite sécurité au cas où l'authentification échouerait pour éviter un crash d'index manquant
        if (isset($response['token'])) {
            $this->client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $response['token']));
        }
    }
}