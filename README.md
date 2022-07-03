# Downloader

Download files using PHP and Curl.

## ✅ Requirements

- PHP 7.0 or newer

## 🔨 Usage

```php
use Nevadskiy\Downloader\CurlDownloader;

$url = 'https://example.com/files/books.zip';
$path = __DIR__.'/storage/books.zip'

$downloader = new CurlDownloader();
$downloader->download($url, $path)
```

## ☕ Contributing

Thank you for considering contributing. Please see [CONTRIBUTING](CONTRIBUTING.md) for more information.

## 📜 License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
