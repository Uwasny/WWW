<?php

declare(strict_types=1);

function productUploadDir(): string
{
    return dirname(__DIR__) . '/public/assets/uploads/products';
}

function deleteProductImageFile(?string $relativePath): void
{
    if (!$relativePath) {
        return;
    }

    $relativePath = str_replace(['..', '\\'], '', $relativePath);
    $fullPath = dirname(__DIR__) . '/public/' . ltrim($relativePath, '/');

    if (is_file($fullPath)) {
        unlink($fullPath);
    }
}

function saveProductImage(int $productId, array $file, ?string $oldPath = null): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        throw new InvalidArgumentException('Nie wybrano pliku.');
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Błąd przesyłania pliku.');
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new RuntimeException('Plik jest za duży (maks. 5 MB).');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Dozwolone formaty: JPG, PNG, GIF, WEBP.');
    }

    $extension = $allowed[$mime];
    $filename = sprintf('product_%d_%s.%s', $productId, bin2hex(random_bytes(8)), $extension);
    $targetPath = productUploadDir() . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Nie udało się zapisać zdjęcia.');
    }

    deleteProductImageFile($oldPath);

    return 'assets/uploads/products/' . $filename;
}
