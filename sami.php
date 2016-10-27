<?php

require 'vendor/autoload.php';

use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Sami;
use Symfony\Component\Finder\Finder;
use Sami\Parser\Filter\TrueFilter;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->in(__DIR__ . '/src');

$sami = new Sami($iterator, array(
    'title' => 'CEWW API',
    'build_dir' => __DIR__ . '/docs/api/sami',
    'cache_dir' => __DIR__ . '/var/cache/sami',
    'default_opened_level' => 2,
));

$sami['filter'] = function () {
    return new TrueFilter();
};

return $sami;
