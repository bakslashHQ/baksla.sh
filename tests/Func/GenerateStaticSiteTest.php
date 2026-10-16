<?php

declare(strict_types=1);

namespace App\Tests\Func;

use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

final class GenerateStaticSiteTest extends KernelTestCase
{
    public function testGenerateAllStaticPages(): void
    {
        $tester = $this->runSsgCommand();

        $this->assertSame(0, $tester->getStatusCode(), $tester->getDisplay());

        $storage = self::getContainer()->get('bakslash_static_site.filesystem');
        $this->assertInstanceOf(FilesystemOperator::class, $storage);

        foreach ([
            'index.html',
            'legal-notices.html',
            'robots.txt',
            // Both declare a route format while returning another Content-Type, so they are
            // the first names to change should a route ever drop its explicit format.
            'llms.txt',
            'blog/feed.xml',
            'sitemap.xml',
            'blog.html',
            'team.html',
            'blog/symfony-certification.html',
        ] as $path) {
            $this->assertTrue($storage->fileExists($path), sprintf('Expected "%s" to be generated.', $path));
        }
    }

    private function runSsgCommand(): CommandTester
    {
        self::bootKernel();

        $this->assertInstanceOf(KernelInterface::class, self::$kernel);
        $application = new Application(self::$kernel);
        $command = $application->find('static-site:generate');
        $tester = new CommandTester($command);
        $tester->execute([
            '--allow-debug' => true,
        ]);

        return $tester;
    }
}
