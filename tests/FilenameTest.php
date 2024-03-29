<?php

namespace Nevadskiy\Downloader\Tests;

use Nevadskiy\Downloader\CurlDownloader;
use Nevadskiy\Downloader\ExtensionGuesser\ExtensionGuesser;
use Nevadskiy\Downloader\FilenameGenerator\FilenameGenerator;

class FilenameTest extends TestCase
{
    /**
     * @test
     */
    public function it_generates_filename_from_url_when_destination_is_directory(): void
    {
        $path = (new CurlDownloader())
            ->download($this->url('/hello-world.txt'), $this->storage);

        static::assertSame($path, $this->storage.'/hello-world.txt');
        static::assertFileEquals(__DIR__.'/fixtures/hello-world.txt', $path);
    }

    /**
     * @test
     */
    public function it_generates_filename_from_url_after_redirects(): void
    {
        $path = (new CurlDownloader())
            ->download($this->url('/redirect'), $this->storage);

        static::assertSame($path, $this->storage.'/hello-world.txt');
        static::assertFileEquals(__DIR__.'/fixtures/hello-world.txt', $path);
    }

    /**
     * @test
     */
    public function it_generates_filename_from_url_and_content_type_when_destination_is_directory(): void
    {
        $path = (new CurlDownloader())
            ->download($this->url('/hello-world'), $this->storage);

        static::assertSame($path, $this->storage.'/hello-world.txt');
        static::assertFileEquals(__DIR__.'/fixtures/hello-world.txt', $path);
    }

    /**
     * @test
     */
    public function it_generates_filename_from_custom_content_type_when_destination_is_directory(): void
    {
        $guesser = $this->createMock(ExtensionGuesser::class);

        $guesser->expects(static::once())
            ->method('getExtension')
            ->with(static::identicalTo('text/plain'))
            ->willReturn('php');

        $path = (new CurlDownloader())
            ->setExtensionGuesser($guesser)
            ->download($this->url('/hello-world'), $this->storage);

        static::assertSame($path, $this->storage.'/hello-world.php');
        static::assertFileEquals(__DIR__.'/fixtures/hello-world.txt', $path);
    }

    /**
     * @test
     */
    public function it_generates_filename_from_content_disposition_header_when_destination_is_directory(): void
    {
        $path = (new CurlDownloader())
            ->download($this->url('/content'), $this->storage);

        static::assertSame($path, $this->storage.'/hello-world.txt');
        static::assertFileEquals(__DIR__.'/fixtures/hello-world.txt', $path);
    }

    /**
     * @test
     */
    public function it_generates_random_filename_when_no_content_type_and_destination_is_directory(): void
    {
        $filenameGenerator = $this->createMock(FilenameGenerator::class);

        $filenameGenerator->expects(static::once())
            ->method('generate')
            ->willReturn('RANDOMFILE');

        $path = (new CurlDownloader())
            ->setRandomFilenameGenerator($filenameGenerator)
            ->download($this->url(), $this->storage);

        static::assertEquals($this->storage.'/RANDOMFILE', $path);
        static::assertStringEqualsFile($path, 'Welcome home!');
    }

    /**
     * @test
     */
    public function it_generates_random_filename_using_default_generator_when_no_content_type_and_destination_is_directory(): void
    {
        $path = (new CurlDownloader())
            ->download($this->url(), $this->storage);

        $filename = substr($path, strlen($this->storage) + 1);

        static::assertSame(32, strlen($filename));
        static::assertStringEqualsFile($path, 'Welcome home!');
    }
}
