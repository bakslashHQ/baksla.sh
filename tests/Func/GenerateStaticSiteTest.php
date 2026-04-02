<?php

declare(strict_types=1);

namespace App\Tests\Func;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

final class GenerateStaticSiteTest extends KernelTestCase
{
    private string $outputDir;

    private Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->filesystem = new Filesystem();
        $this->outputDir = (string) self::getContainer()->getParameter('app.ssg_output_dir');

        if ($this->filesystem->exists($this->outputDir)) {
            $this->filesystem->remove($this->outputDir);
        }
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->filesystem->exists($this->outputDir)) {
            $this->filesystem->remove($this->outputDir);
        }
    }

    public function testGenerateAllStaticPages(): void
    {
        $tester = $this->runCommand();

        $this->assertSame(0, $tester->getStatusCode(), $tester->getDisplay());

        $this->assertFileExists($this->outputDir . '/index.html');
        $this->assertFileExists($this->outputDir . '/legal-notices.html');
        $this->assertFileExists($this->outputDir . '/robots.txt');
        $this->assertFileExists($this->outputDir . '/sitemap.xml');
        $this->assertFileExists($this->outputDir . '/blog.html');
        $this->assertFileExists($this->outputDir . '/team.html');
        $this->assertFileExists($this->outputDir . '/blog/symfony-certification.html');
    }

    public function testDryRunDoesNotDumpFiles(): void
    {
        $tester = $this->runCommand([
            '--dry-run' => true,
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertDirectoryDoesNotExist($this->outputDir);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function runCommand(array $input = []): CommandTester
    {
        $this->assertInstanceOf(KernelInterface::class, self::$kernel);
        $application = new Application(self::$kernel);
        $command = $application->find('ssg:generate');
        $tester = new CommandTester($command);
        $tester->execute($input);

        return $tester;
    }
}
