<?php

namespace Nevadskiy\Downloader;

/**
 * @TODO add helper methods for most common cURL options.
 */
class CurlDownloader implements Downloader
{
    /**
     * The cURL options array.
     *
     * @var array
     */
    protected $options;

    /**
     * The cURL handle callbacks.
     *
     * @var array
     */
    protected $curlHandleCallbacks = [];

    /**
     * Make a new downloader instance.
     */
    public function __construct(array $options = [])
    {
        $this->options = $this->options() + $options;
    }

    /**
     * The default cURL options.
     * @TODO consider filtering reserved options that can break curl downloading configuration.
     */
    protected function options(): array
    {
        return [
            CURLOPT_FAILONERROR => true,
        ];
    }

    /**
     * Add a cURL option with the given value.
     *
     * @return void
     */
    public function withOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * Add a cURL handle callback.
     *
     * @return void
     */
    public function withCurlHandle(callable $callback)
    {
        $this->curlHandleCallbacks[] = $callback;
    }

    /**
     * @inheritdoc
     */
    public function download(string $url, string $path)
    {
        // TODO: validate URL.
        // TODO: validate path.

        // TODO: add possibility to use path as directory and automatically define file name.

        $stream = fopen($path, 'wb+');

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_FILE, $stream);

        foreach ($this->options as $option => $value) {
            curl_setopt($ch, $option, $value);
        }

        foreach ($this->curlHandleCallbacks as $callback) {
            $callback($ch);
        }

        $response = curl_exec($ch);

        curl_close($ch);

        fclose($stream);

        if (! $response) {
            // clean up after failed response.
            unlink($path);

            // TODO: consider also throwing exception with error content
        }
    }

    /**
     * Guess the file name by the given URL.
     */
    protected function guessFileName(string $url): string
    {
        $position = strrpos($url, '/');

        if ($position === false) {
            // TODO: provide default file name (probably check file mime-type).
        }

        return substr($url, $position + 1);
    }

    /**
     * Get the file path by the given file name and directory.
     */
    protected function getFilePath(string $directory, string $name): string
    {
        return rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . trim($name, DIRECTORY_SEPARATOR);
    }
}
