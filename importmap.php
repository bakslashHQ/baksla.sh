<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    'analytics' => [
        'version' => '0.8.16',
    ],
    '@analytics/google-analytics' => [
        'version' => '1.1.0',
    ],
    '@analytics/core' => [
        'version' => '0.12.17',
    ],
    '@analytics/storage-utils' => [
        'version' => '0.4.2',
    ],
    'analytics-utils' => [
        'version' => '1.0.14',
    ],
    '@analytics/global-storage-utils' => [
        'version' => '0.1.7',
    ],
    '@analytics/type-utils' => [
        'version' => '0.6.2',
    ],
    '@analytics/cookie-utils' => [
        'version' => '0.2.12',
    ],
    '@analytics/localstorage-utils' => [
        'version' => '0.1.10',
    ],
    '@analytics/session-storage-utils' => [
        'version' => '0.0.7',
    ],
    'dlv' => [
        'version' => '1.1.3',
    ],
];
