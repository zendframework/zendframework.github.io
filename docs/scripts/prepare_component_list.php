<?php

const TEMPLATE = <<< 'EOT'
<h2>%group%</h2>

<div class="components">
  %components%
</div>
EOT;

const PACKAGE_TEMPLATE = <<< 'EOT'
  <div class="component">
    <h4><a href="%href%">%name%</a></h4>
    <p class="package">%package%</p>
    <p>%description%</p>
  </div>
EOT;

$root      = realpath(getcwd());
$listFile  = sprintf('%s/zf-component-list.json', $root);
$srcPath   = sprintf('%s/docs/book', $root);
$distPath  = sprintf('%s/index.html.dist', $srcPath);
$indexPath = sprintf('%s/index.html', $srcPath);

if (! is_readable($listFile)) {
    fwrite(STDERR, "Unable to locate zf-component-list.json in root directory; did you run 'make'?\n");
    exit(1);
}

if (! is_readable($distPath)) {
    fwrite(STDERR, "Unable to locate docs/book/index.html.dist; did you run this script from the correct directory?\n");
    exit(1);
}

$json = file_get_contents($listFile);
$packages = json_decode($json, true);

// Group packages by type
$packagesByType = [
    'learn' => [
        'title'    => 'Learn ZF',
        'packages' => [],
    ],
    'mvc' => [
        'title'    => 'MVC Framework',
        'packages' => [],
    ],
    'middleware' => [
        'title'    => 'Expressive and PSR-15 Middleware',
        'packages' => [],
    ],
    'projects' => [
        'title'    => 'Tooling and Composer Plugins',
        'packages' => [],
    ],
    'components' => [
        'title'    => 'Components',
        'packages' => [],
    ],
];

$types = array_keys($packagesByType);

// Sort packages into various groups, generating markup for each as we do.
$packagesByType = array_reduce(
    $packages,
    function ($grouped, $package) use ($types) {
        if (! isset($package['group']) || ! in_array($package['group'], $types, true)) {
            $package['group'] = 'components';
        }

        $grouped[$package['group']]['packages'][] = str_replace(
            [       '%href%',         '%name%',         '%package%',         '%description%'],
            [$package['url'], $package['name'], $package['package'], $package['description']],
            PACKAGE_TEMPLATE
        );

        return $grouped;
    },
    $packagesByType
);

// Generate per-group markup
$markup = array_reduce(
    $packagesByType,
    function ($markup, $group) {
        $markup []= str_replace(
            [      '%group%',                      '%components%'],
            [$group['title'], implode("\n\n", $group['packages'])],
            TEMPLATE
        );
        return $markup;
    },
    []
);

$markup = implode("\n\n", $markup);

copy($distPath, $indexPath);
$fh = fopen($indexPath, 'a');
fwrite($fh, $markup);
fclose($fh);

printf("Generated %s\n", $indexPath);
