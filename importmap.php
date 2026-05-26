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
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'analytics' => [
        'version' => '0.8.19',
    ],
    '@analytics/google-analytics' => [
        'version' => '1.1.0',
    ],
    '@analytics/core' => [
        'version' => '0.13.2',
    ],
    '@analytics/storage-utils' => [
        'version' => '0.4.4',
    ],
    'analytics-utils' => [
        'version' => '1.1.1',
    ],
    '@analytics/global-storage-utils' => [
        'version' => '0.1.9',
    ],
    '@analytics/type-utils' => [
        'version' => '0.6.4',
    ],
    '@analytics/cookie-utils' => [
        'version' => '0.2.14',
    ],
    '@analytics/localstorage-utils' => [
        'version' => '0.1.12',
    ],
    '@analytics/session-storage-utils' => [
        'version' => '0.0.9',
    ],
    'dlv' => [
        'version' => '1.1.3',
    ],
];
