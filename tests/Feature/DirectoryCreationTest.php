<?php

namespace Nevadskiy\Downloader\Tests\Feature;

use Nevadskiy\Downloader\Exceptions\DirectoryMissingException;
use Nevadskiy\Downloader\SimpleDownloader;
use Nevadskiy\Downloader\Tests\TestCase;

class DirectoryCreationTest extends TestCase
{
    // @todo test when destination path is $this->storage.'/files/2008/02/08' (not a file but missing directory)

    /** @test */
    public function it_throws_exception_when_destination_directory_is_missing()
    {
        try {
            (new SimpleDownloader())
                ->download($this->serverUrl('/fixtures/hello-world.txt'), $this->storage.'/files/hello-world.txt');

            static::fail(sprintf('Expected [%s] was not thrown.', DirectoryMissingException::class));
        } catch (DirectoryMissingException $e) {
            self::assertSame(sprintf('Directory [%s] does not exits.', $this->storage.'/files'), $e->getMessage());
        }
    }

    /** @test */
    public function it_creates_destination_directory_when_it_is_missing()
    {
        $destination = (new SimpleDownloader())
            ->allowDirectoryCreation()
            ->download($this->serverUrl('/fixtures/hello-world.txt'), $this->storage.'/files/hello-world.txt');

        static::assertSame($this->storage.'/files/hello-world.txt', $destination);
        static::assertFileEquals(__DIR__.'/../server/fixtures/hello-world.txt', $destination);
    }

    /** @test */
    public function it_creates_destination_directory_recursively_when_it_is_missing()
    {
        $destination = (new SimpleDownloader())
            ->allowRecursiveDirectoryCreation()
            ->download($this->serverUrl('/fixtures/hello-world.txt'), $this->storage.'/files/2022/07/26/hello-world.txt');

        static::assertSame($this->storage.'/files/2022/07/26/hello-world.txt', $destination);
        static::assertFileEquals(__DIR__.'/../server/fixtures/hello-world.txt', $destination);
    }
}
