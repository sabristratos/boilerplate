<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    |
    | A list of all MIME types that are allowed to be uploaded.
    | This is used by the RealMimeType validation rule to ensure that
    | only safe file types are uploaded to the server.
    |
    | For certain image formats like HEIC/HEIF, you will need to have
    | the 'imagick' PHP extension installed and properly configured on your
    | server with the appropriate libraries (e.g., libheif).
    |
    */
    'allowed_mime_types' => [
        // Images
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/svg+xml',
        'image/webp',
        'image/heic',
        'image/heif',

        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'text/csv',

        // Archives
        'application/zip',
        'application/x-rar-compressed',
    ],
]; 