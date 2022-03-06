<?php

declare(strict_types=1);

namespace Verzameldwerk\Bundle\AkismetBundle\Tests\Functional\Akismet\Infrastructure\Sulu\Admin;

use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Finder\Finder;

/**
 * @coversNothing
 */
class MetadataTest extends SuluTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createAuthenticatedClient();

        $this->purgeDatabase();
        $this->initPhpcr();
    }

    /**
     * @dataProvider loadFormKeys
     */
    public function testFormMetadata(string $formKey): void
    {
        $this->client->request('GET', '/admin/metadata/form/'.$formKey);

        $this->assertHttpStatusCode(200, $this->client->getResponse());
    }

    /**
     * @dataProvider loadListKeys
     */
    public function testListMetadata(string $listKey): void
    {
        $this->client->request('GET', '/admin/metadata/list/'.$listKey);

        $this->assertHttpStatusCode(200, $this->client->getResponse());
    }

    public function testConfig(): void
    {
        $this->client->request('GET', '/admin/config');

        $this->assertHttpStatusCode(200, $this->client->getResponse());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function loadFormKeys()
    {
        return $this->getFileKeys('forms');
    }

    /**
     * @return \Generator<array<string>>
     */
    public function loadListKeys(): \Generator
    {
        return $this->getFileKeys('lists');
    }

    /**
     * @return \Generator<array<string>>
     */
    private function getFileKeys(string $type): \Generator
    {
        $finder = new Finder();
        $path = __DIR__.'/../../../../../../config/'.$type;

        $path = realpath($path);
        if (!$path) {
            throw new \RuntimeException(sprintf('Could not find path: "%s"', $path));
        }

        $finder->files()->in($path);

        foreach ($finder as $file) {
            yield [
                $file->getFilenameWithoutExtension(),
            ];
        }
    }
}