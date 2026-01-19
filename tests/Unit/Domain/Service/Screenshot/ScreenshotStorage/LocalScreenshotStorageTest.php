<?php

namespace App\Tests\Unit\Domain\Service\Screenshot\ScreenshotStorage;


use App\Domain\Service\Screenshot\ScreenshotStorage\LocalScreenshotStorage;
use PHPUnit\Framework\TestCase;
use SplFileObject;

class LocalScreenshotStorageTest extends TestCase
{
    private string $tempDirectory;
    private LocalScreenshotStorage $storage;

    protected function setUp(): void
    {
        $this->tempDirectory = sys_get_temp_dir() . '/screenshots_test_' . uniqid();
        mkdir($this->tempDirectory, 0775, true);

        $this->storage = new LocalScreenshotStorage($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->tempDirectory);
    }

    /* store method */

    public function testStoreCreatesFileWithCorrectContent(): void
    {
        // Mock data
        $sourcePath = $this->tempDirectory . '/source.txt';
        file_put_contents($sourcePath, 'hello screenshot');
        $file = new SplFileObject($sourcePath, 'rb');
        $key = 'EURUSD/H1/screenshot.txt';
        $storedPath = $this->tempDirectory . '/' . $key;

        // Start test
        $this->storage->store($key, $file);

        // Assertions
        $this->assertFileExists($storedPath);
        $this->assertSame(
            'hello screenshot',
            file_get_contents($storedPath)
        );
    }

    /* read method */

    public function testReadReturnsSplFileObject(): void
    {
        // Mock data
        $key = 'test/read.txt';
        $path = $this->tempDirectory . '/' . $key;
        mkdir(dirname($path), 0775, true);
        file_put_contents($path, 'read content');

        // Start test
        $file = $this->storage->read($key);

        // Assertions
        $this->assertInstanceOf(SplFileObject::class, $file);
        $this->assertSame('read content', $file->fread(1024));
    }

    /* delete method */

    public function testDeleteRemovesFile(): void
    {
        // Mock data
        $key = 'test/delete.txt';
        $path = $this->tempDirectory . '/' . $key;
        mkdir(dirname($path), 0775, true);
        file_put_contents($path, 'to delete');

        // Assert
        $this->assertFileExists($path);

        // Start test
        $this->storage->delete($key);

        // Assert
        $this->assertFileDoesNotExist($path);
    }

    /* ------------------ helpers ------------------ */

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
