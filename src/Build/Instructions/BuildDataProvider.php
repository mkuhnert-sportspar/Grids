<?php

namespace Nayjest\Grids\Build\Instructions;

use LogicException;
use Nayjest\Builder\Instructions\Base\Instruction;
use Nayjest\Builder\Scaffold;
use Nayjest\Grids\DataProvider\CollectionDataProvider;
use Nayjest\Grids\DataProvider\DataProviderInterface;
use Nayjest\Grids\DbalDataProvider;
use Nayjest\Grids\EloquentDataProvider;

/**
 * Class BuildDataProvider
 *
 * This class is a build instruction for nayjest/build package
 * that defines how to setup grids data provider
 *
 * @internal
 * @package Nayjest\Grids\Build\Instructions
 */
class BuildDataProvider extends Instruction
{
    protected $phase = self::PHASE_PRE_INST;

    /**
     * @param Scaffold $scaffold
     * @throws LogicException
     */
    public function apply(Scaffold $scaffold)
    {
        $src = $scaffold->getInput('src');
        $scaffold->excludeInput('src');
        $class = null;
        $arg = null;

        // If we have a provider, then we're good to go
        if (is_object($src) && $src instanceof DataProviderInterface) {
            $scaffold->input['data_provider'] = $src;
            return;
        }

        if (is_object($src)) {
            $providerClasses = [
                EloquentDataProvider::class,
                DbalDataProvider::class,
                CollectionDataProvider::class
            ];

            /** @var DataProviderInterface $providerClass */
            foreach ($providerClasses as $providerClass) {
                if ($providerClass::canProvideFor($src)) {
                    $class = $providerClass;
                    $arg = $src;
                }
            }

        } elseif (is_string($src)) {
            // model name
            if (
                class_exists($src, true) &&
                is_subclass_of($src, '\Illuminate\Database\Eloquent\Model')
            ) {
                $class = EloquentDataProvider::class;
                $model = new $src;
                $arg = $model->newQuery();
            }
        }

        if ($class !== null && $arg !== null) {
            $provider = new $class($arg);
            $scaffold->input['data_provider'] = $provider;
        } else {
            throw new LogicException('Invalid Data Provider Configuration');
        }
    }
}

