<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;

final class GzipRotatingFileHandler extends RotatingFileHandler
{
    private string $basePath;

    public function __construct(
        string $filename,
        int $maxFiles = 0,
        int|Level $level = Level::Debug,
        bool $bubble = true,
        ?int $filePermission = null,
        bool $useLocking = false,
        string $dateFormat = self::FILE_PER_DAY
    ) {
        $this->basePath = $filename;
        parent::__construct($filename, $maxFiles, $level, $bubble, $filePermission, $useLocking, $dateFormat);
    }

    protected function rotate(): void
    {
        parent::rotate();
        $this->compressOldLogs();
    }

    private function compressOldLogs(): void
    {
        $dir = dirname($this->basePath);
        $name = pathinfo($this->basePath, PATHINFO_FILENAME);
        $extension = pathinfo($this->basePath, PATHINFO_EXTENSION);
        $currentDate = (new \DateTimeImmutable())->format($this->dateFormat);
        $current = $dir . '/' . $name . '-' . $currentDate . ($extension !== '' ? '.' . $extension : '');
        $pattern = $dir . '/' . $name . '-*' . ($extension !== '' ? '.' . $extension : '');

        foreach (glob($pattern) as $file) {
            if ($file === $current || str_ends_with($file, '.gz')) {
                continue;
            }

            $this->gzipFile($file, $file . '.gz');
        }
    }

    private function gzipFile(string $source, string $destination): void
    {
        $in = fopen($source, 'rb');
        if ($in === false) {
            return;
        }

        $out = gzopen($destination, 'wb9');
        if ($out === false) {
            fclose($in);
            return;
        }

        while (!feof($in)) {
            $buffer = fread($in, 1024 * 512);
            if ($buffer === false) {
                break;
            }
            gzwrite($out, $buffer);
        }

        fclose($in);
        gzclose($out);
        unlink($source);
    }
}
