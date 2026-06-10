<?php

namespace App\Tests\Integration;

use App\Tests\Integration\Factory\UserFactory;
use App\Tests\Service\TestPasswordHasher;

/**
 * Classe de base pour les tests d'integration du firewall frontApi (JWT).
 * Etend AbstractTestApiController pour reutiliser le reset de schema et
 * les assertions JSON, et ajoute la gestion du token JWT.
 */
abstract class AbstractFrontApiTestController extends AbstractTestApiController
{
    private string $jwtToken = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->jwtToken = ''; // invalider le token apres chaque reset de schema
    }

    /**
     * Retourne les headers HTTP avec le token JWT.
     * Cree l'utilisateur de test et effectue le login si necessaire.
     */
    protected function getJwtHeaders(): array
    {
        if ($this->jwtToken === '') {
            $this->jwtToken = $this->fetchJwtToken();
        }
        return ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->jwtToken];
    }

    /**
     * Effectue une requete frontApi authentifiee.
     */
    protected function requestFront(string $method, string $path, ?string $content = null): void
    {
        $headers = $this->getJwtHeaders();
        if ($content !== null) {
            $headers['CONTENT_TYPE'] = 'application/json';
        }
        $this->requestUrl($method, $path, $headers, $content);
    }

    /**
     * Effectue une requete frontApi SANS authentication.
     */
    protected function requestFrontWithoutAuth(string $method, string $path, ?string $content = null): void
    {
        $headers = [];
        if ($content !== null) {
            $headers['CONTENT_TYPE'] = 'application/json';
        }
        $this->requestUrl($method, $path, $headers, $content);
    }

    // ── Privé ─────────────────────────────────────────────────────

    private function fetchJwtToken(): string
    {
        $hasher = static::getContainer()->get(TestPasswordHasher::class);
        UserFactory::create($this->em, $hasher, 'front@test.com', 'password', ['ROLE_USER']);

        $this->client->request(
            'POST',
            '/frontApi/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'front@test.com', 'password' => 'password'])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);
        return $data['token'] ?? '';
    }
}
