<?php
return array(
    'repo' => 'Main',
    'name' => 'sample', // Lowercase package name
    'displayName' => 'Sample', // Package name in your MODX Repository
    'version' => $pp->getLatestVersion('sample'),
    'dir' => '_packages',
    'description' => 'Sample Package', // Description for that package
    'author' => 'Sample', // Package author
    'modx_version' => '2.3', // Minimal MODX version for that package
    'users' => '' // Comma separated list of usernames that name could see/download the page
);
