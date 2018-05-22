<?php

const TEMPLATE = <<< 'EOT'
<div class="row">
  <h2>Components</h2>

  %components%
</div>

EOT;

const PACKAGE_TEMPLATE = <<< 'EOT'
  <div class="component col-xs-12 col-md-6">
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

$componentMarkup = [];
foreach ($packages as $package) {
    // @codingStandardsIgnoreStart
    $componentMarkup []= str_replace(
        [       '%href%',         '%name%',         '%package%',         '%description%'],
        [$package['url'], $package['name'], $package['package'], $package['description']],
        PACKAGE_TEMPLATE
    );
}
$componentMarkup = implode("\n\n", $componentMarkup);

$markup = str_replace('%components%', $componentMarkup, TEMPLATE);

copy($distPath, $indexPath);
$fh = fopen($indexPath, 'a');
fwrite($fh, $markup);
fclose($fh);

printf("Generated %s\n", $indexPath);
