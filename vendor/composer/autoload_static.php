<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitff8d7cff6aca555c12eaf624e56e7008
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LRC\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LRC\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitff8d7cff6aca555c12eaf624e56e7008::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitff8d7cff6aca555c12eaf624e56e7008::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitff8d7cff6aca555c12eaf624e56e7008::$classMap;

        }, null, ClassLoader::class);
    }
}
