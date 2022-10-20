<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Word;
use App\Repository\WordsRepository;

class WordsApiTest extends ApiTestCase
{
    // Test sur get all words
    private WordsRepository $wordsRepository;

    public function testGetCollectionWords(): void
    {
        $response = static::createClient()->request('GET', '/api/word');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Word',
            '@id' => '/api/word',
            '@type' => 'hydra:Collection',
        ]);
        $this->assertMatchesResourceCollectionJsonSchema(Word::class);
    }

    //Test sur l'ajoute d'un word
    public function testCreateWords(): void
    {
        $response = static::createClient()->request('POST', '/api/words', ['json' => [
            'mot' => 'àjoutéôè',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Words',
            '@type' => 'Words',
            'mot' => 'àjoutéôè',
        ]);
        $this->assertMatchesResourceItemJsonSchema(Words::class);
    }

    // Test sur l'ajoute d'un invalide word
    public function testCreateInvalidWords(): void
    {
        static::createClient()->request('POST', '/api/words', ['json' => [
            'mot' => '?./#1_invalid',
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'mot: This value is not valid.',
        ]);
    }

    // Test sur le nombre total des ligneq sur l'entity Words
    public function testCountWords(): void
    {
        $this->countWords = static::getContainer()->get('doctrine')->getRepository(Words::class);
        $response = static::createClient()->request('GET', '/api/list');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'count' => count($this->countWords->findAll()),
        ]);
    }

    // Bloqué la method get by id
    public function testBlockGetByID(): void
    {
        $response = static::createClient()->request('GET', '/api/words/id');
        $this->assertResponseStatusCodeSame(404);
    }

    // Bloqué la method Put
    public function testBlockPut(): void
    {
        $response = static::createClient()->request('PUT', '/api/words');
        $this->assertResponseStatusCodeSame(405);
    }

    // Bloqué la method Patsh
    public function testBlockPatsh(): void
    {
        $response = static::createClient()->request('PATCH', '/api/words');
        $this->assertResponseStatusCodeSame(405);
    }

    // Bloqué la method Delete
    public function testBlockDelete(): void
    {
        $response = static::createClient()->request('DELETE', '/api/word');
        $this->assertResponseStatusCodeSame(405);
    }
}

