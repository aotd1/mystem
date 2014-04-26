<?php

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Installer\InstallationManager;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\Repository\WritableRepositoryInterface;
use Composer\Package\PackageInterface;
use Composer\Script\Event;
use Composer\Package\Package;


class MystemBinaryInstaller
{

    /**
     * @var Composer $composer
     */
    public static $composer;

    /**
     * @var IOInterface $io
     */
    public static $io;

    /**
     * @var InstallationManager $installer
     */
    public static $installer;

    /**
     * @var WritableRepositoryInterface $localRepo
     */
    public static $localRepo;

    /**
     * @var \Composer\Package\RootPackage $packages
     */
    public static $config;

    /**
     * @var Package[] platform-specific packages to install
     */
    public static $toInstall = array();

    public static function init(Event $event)
    {
        self::$composer = $event->getComposer();
        self::$io = $event->getIO();
        self::$installer = $event->getComposer()->getInstallationManager();
        self::$localRepo = $event->getComposer()->getRepositoryManager()->getLocalRepository();

        $fileName = __DIR__ . '/composer-platform-specific.json';
        if (!file_exists($fileName)) {
            self::$io->write("<error>File $fileName not exists.</error>");
            return false;
        }
        self::$config = json_decode(file_get_contents($fileName), true);

        if (!isset(self::$config['extra']['platform-specific-packages'])){
            return false;
        }

        //@TODO: refactor it all to use composer.lock file, to track updated platform-specific packages
        self::$toInstall = array();

        $unresolved = array();
        foreach (self::$config['extra']['platform-specific-packages'] as $name => $variants) {
            $package = self::createPlatformSpecificPackage($name, $variants);
            if ($package) {
                self::$toInstall[] = $package;
            } else {
                $unresolved[] = $name;
            }
        }

        if (!empty($unresolved)) {
            self::$io->write('<error>Your requirements could not be resolved for current OS and/or processor architecture.</error>');
            self::$io->write("\n  Unresolved platform-specific packages:");
            foreach ($unresolved as $name) {
                self::$io->write("    - $name");
            }
        }

        return true;
    }

    public static function install(Event $event)
    {

        if (!self::init($event)) {
            return;
        }

        $notInstalled = 0;
        if (!empty(self::$toInstall)) {
            self::$io->write('<info>Installing platform-specific dependencies</info>');
            foreach (self::$toInstall as $package) {
                if (!self::$installer->isPackageInstalled(self::$localRepo, $package)) {
                    self::$installer->install(self::$localRepo, new InstallOperation($package));
                } else {
                    $notInstalled++;
                }
            }
        }
        if (empty(self::$toInstall) || $notInstalled > 0) {
            self::$io->write('Nothing to install or update in platform-specific dependencies');
        }
    }

    public static function update(Event $event)
    {
        //@TODO: update changed packages
        self::install($event);
    }

    /**
     * @param string $packageName
     * @param array $variants
     * @return null|Package
     */
    protected static function createPlatformSpecificPackage($packageName, $variants)
    {
        foreach ($variants as $variant) {
            if (!empty($variant['architecture']) && $variant['architecture'] !== self::getArchitecture())
                continue;

            if (!empty($variant['os']) && $variant['os'] !== self::getOS())
                continue;

            reset($variant);
            $name = key($variant);
            $version = $variant[$name];

            return self::createPackage($name, $version, $packageName);
        }

        return null;
    }

    /**
     * @param string $name
     * @param string $version
     * @param string $newName
     * @return null|Package
     */
    protected static function createPackage($name, $version, $newName)
    {
        if (!isset(self::$config['repositories'])) {
            return null;
        }
        $package = null;
        foreach (self::$config['repositories'] as $cursor) {
            if (isset($cursor['package']['name'], $cursor['package']['version']) &&
                $cursor['package']['name'] === $name &&
                ($version === '*' || $cursor['package']['version'] === $version)
            ) {
                $package = $cursor['package'];
                break;
            }
        }
        if (!$package) {
            return null;
        }
        $new = new Package($newName, $package['version'], $package['version']);
        $new->setType('dist');
        if (isset($package['bin'])) {
            $new->setBinaries($package['bin']);
        }
        if (isset($package['dist']['type'])) {
            $new->setDistType($package['dist']['type']);
        }
        if (isset($package['dist']['url'])) {
            $new->setDistUrl($package['dist']['url']);
        }
        if (isset($package['excludes'])) {
            $new->setArchiveExcludes($package['excludes']);
        }
        self::$localRepo->addPackage($new);
        return $new;
    }

    /**
     * Returns the Operating System.
     *
     * @return string OS, e.g. macosx, freebsd, windows, linux.
     */
    public static function getOS()
    {
        $uname = strtolower(php_uname());

        if (strpos($uname, "darwin") !== false) {
            return 'macosx';
        } elseif (strpos($uname, "win") !== false) {
            return 'windows';
        } elseif (strpos($uname, "freebsd") !== false) {
            return 'freebsd';
        } elseif (strpos($uname, "linux") !== false) {
            return 'linux';
        } else {
            return 'undefined';
        }
    }

    /**
     * Returns the Architecture.
     *
     * @return string BitSize, e.g. i386, x64.
     */
    public static function getArchitecture()
    {
        switch (PHP_INT_SIZE) {
            case 4:
                return 'i386';
            case 8:
                return 'x64';
            default:
                return 'undefined';
        }
    }

} 