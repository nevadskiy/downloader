<?php

namespace Nevadskiy\Downloader\Tests\Feature;

use DateTime;
use Nevadskiy\Downloader\Exceptions\DestinationFileMissingException;
use Nevadskiy\Downloader\Exceptions\FileExistsException;
use Nevadskiy\Downloader\Filename\FilenameGenerator;
use Nevadskiy\Downloader\SimpleDownloader;
use Nevadskiy\Downloader\Tests\TestCase;

class ClobberDownloaderTest extends TestCase
{
    /** @test */
    public function it_throws_exception_when_file_already_exists()
    {
        $destination = $this->storage.'/hello-world.txt';

        file_put_contents($destination, 'Old content!');

        $tempGenerator = $this->createMock(FilenameGenerator::class);

        $tempGenerator->expects(static::once())
            ->method('generate')
            ->willReturn('TEMPFILE');

        try {
            (new SimpleDownloader())
                ->setTempFilenameGenerator($tempGenerator)
                ->download($this->serverUrl('/fixtures/hello-world.txt'), $destination);

            static::fail(sprintf('Expected [%s] was not thrown.', FileExistsException::class));
        } catch (FileExistsException $e) {
            static::assertStringEqualsFile($destination, 'Old content!');
            static::assertFileNotExists($this->storage.'/TEMPFILE');
        }
    }

    /** @test */
    public function it_skips_dowloading_when_file_already_exists()
    {
        $destination = $this->storage.'/hello-world.txt';

        file_put_contents($destination, 'Old content!');

        $destination = (new SimpleDownloader())
            ->skipIfExists()
            ->download($this->serverUrl('/fixtures/hello-world.txt'), $destination);

        static::assertSame($this->storage.'/hello-world.txt', $destination);
        static::assertStringEqualsFile($destination, 'Old content!');
    }

    /** @test */
    public function it_replace_content_of_existing_file()
    {
        $destination = $this->storage.'/hello-world.txt';

        file_put_contents($destination, 'Old content!');

        (new SimpleDownloader())
            ->replaceIfExists()
            ->download($this->serverUrl('/fixtures/hello-world.txt'), $destination);

        static::assertSame($this->storage.'/hello-world.txt', $destination);
        static::assertFileEquals(__DIR__.'/../server/fixtures/hello-world.txt', $destination);
    }

    /** @test */
    public function it_updates_old_content_of_existing_file()
    {
        $destination = $this->storage.'/hello-world.txt';

        file_put_contents($destination, 'Old content!');

        touch($destination, DateTime::createFromFormat('m/d/Y', '1/10/2014')->getTimestamp());

        (new SimpleDownloader())
            ->updateIfExists()
            ->download($this->serverUrl('/fixtures/hello-world.txt'), $destination);

        static::assertSame($this->storage.'/hello-world.txt', $destination);
        static::assertFileEquals(__DIR__.'/../server/fixtures/hello-world.txt', $destination);
    }

    /** @test */
    public function it_does_not_update_old_content_when_file_already_exists_and_has_newer_modification_date()
    {
        $destination = $this->storage.'/hello-world.txt';

        file_put_contents($destination, 'Old content!');

        $destination = (new SimpleDownloader())
            ->updateIfExists()
            ->download($this->serverUrl('/fixtures/hello-world.txt'), $destination);

        static::assertSame($this->storage.'/hello-world.txt', $destination);
        static::assertStringEqualsFile($destination, 'Old content!');
    }

    /** @test */
    public function it_throws_exception_when_destination_is_directory()
    {
        file_put_contents($this->storage.'/hello-world.txt', 'Old content!');

        try {
            (new SimpleDownloader())
                ->updateIfExists()
                ->download($this->serverUrl('/fixtures/hello-world.txt'), $this->storage);

            static::fail(sprintf('Expected [%s] was not thrown.', DestinationFileMissingException::class));
        } catch (DestinationFileMissingException $e) {
            static::assertStringEqualsFile($this->storage.'/hello-world.txt', 'Old content!');
        }
    }

    // @todo use response timestamp header.
}
