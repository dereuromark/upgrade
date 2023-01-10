<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(\Rector\Php74\Rector\Property\TypedPropertyRector::class, ['inline_public' => true]);
};
