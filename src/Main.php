<?php
/**
 * Copyright (c) 2014, Praxigento
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and the following
 *      disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the
 *      following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
/**
 * Main class for the plugin.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Composer\Plugin\Templates;


class Main
    implements
    \Composer\Plugin\PluginInterface,
    \Composer\EventDispatcher\EventSubscriberInterface
{
    /** Entry name for plugin config file in 'extra' section of composer.json */
    const EXTRA_PARAM = 'praxigento_templates_config';

    /**#@+
     *
     * Names of the event handlers (this class' methods).
     */
    const HNDL_COMMAND = 'onCommand';
    const HNDL_INSTALLER = 'onInstaller';
    const HNDL_PACKAGE = 'onPackage';
    /**#@- */

    /**
     * @var array Map events to handlers.
     */
    private static $EVENT_HANDLERS = [
        'pre-install-cmd' => self::HNDL_COMMAND,
        'post-install-cmd' => self::HNDL_COMMAND,
        'pre-update-cmd' => self::HNDL_COMMAND,
        'post-update-cmd' => self::HNDL_COMMAND,
        'post-status-cmd' => self::HNDL_COMMAND,
        'pre-archive-cmd' => self::HNDL_COMMAND,
        'post-archive-cmd' => self::HNDL_COMMAND,
        'pre-autoload-dump' => self::HNDL_COMMAND,
        'post-autoload-dump' => self::HNDL_COMMAND,
        'post-root-package-install' => self::HNDL_COMMAND,
        'post-create-project-cmd' => self::HNDL_COMMAND,
        'pre-dependencies-solving' => self::HNDL_INSTALLER,
        'post-dependencies-solving' => self::HNDL_INSTALLER,
        'pre-package-install' => self::HNDL_PACKAGE,
        'post-package-install' => self::HNDL_PACKAGE,
        'pre-package-update' => self::HNDL_PACKAGE,
        'post-package-update' => self::HNDL_PACKAGE,
        'pre-package-uninstall' => self::HNDL_PACKAGE,
        'post-package-uninstall' => self::HNDL_PACKAGE,
        'init' => 'onPluginInit',
        'command' => 'onPluginCommand',
        'pre-file-download' => 'onPluginPreFileDownload',
    ];
    /** @var \Composer\Composer */
    private $composer;
    /** @var \Praxigento\Composer\Plugin\Templates\Config */
    private static $config;
    /** @var  string|array Names of the plugin's configuration files. */
    private $configFileNames;
    /** @var \Composer\IO\IOInterface */
    private $io;

    /**
     * Common handler for all events.
     *
     * @param \Composer\EventDispatcher\Event $event
     */
    protected function _onEvent(\Composer\EventDispatcher\Event $event)
    {
        $name = $event->getName();
        if (self::$config) {
            $templates = self::$config->getTemplatesForEvent($name);
            $vars = self::$config->getVars();
            $hndl = new \Praxigento\Composer\Plugin\Templates\Handler($vars, $this->io);
            foreach ($templates as $one) {
                /* process one template */
                $hndl->process($one);
            }
        }
    }

    /**
     * Apply plugin modifications to composer
     *
     * See http://symfony.com/doc/current/components/console/introduction.html for more about $io
     *      Available tags are: [info|comment|question|error]
     *
     * @param \Composer\Composer $composer
     * @param \Composer\IO\IOInterface $io
     */
    public function activate(
        \Composer\Composer $composer,
        \Composer\IO\IOInterface $io
    ) {
        $this->composer = $composer;
        $this->io = $io;
        $extra = $composer->getPackage()->getExtra();
        if (isset($extra[self::EXTRA_PARAM])) {
            $files = $extra[self::EXTRA_PARAM];
            /* parse configuration files */
            if (!is_array($files)) {
                $this->configFileNames = [$files];
            } else {
                $this->configFileNames = $files;
            }
            foreach ($this->configFileNames as $one) {
                if (file_exists($one)) {
                    $config = new \Praxigento\Composer\Plugin\Templates\Config($one);
                    if ($config->hasRawData()) {
                        $io->write(__CLASS__ . ": <info>Configuration is read from '$one'.</info>", true);
                        if (is_null(self::$config)) {
                            self::$config = $config;
                        } else {
                            self::$config->merge($config);
                        }
                    } else {
                        $io->writeError(__CLASS__ . ": <error>Cannot read valid JSON from configuration file '$one'. Plugin will be disabled.</error>",
                            true);
                        self::$config = null;
                        break;
                    }
                } else {
                    $io->writeError(__CLASS__ . ": <error>Cannot open configuration file '$one'. Plugin will be disabled.</error>",
                        true);
                    self::$config = null;
                    break;
                }
            }
        } else {
            $io->writeError(__CLASS__ . ": <error>Extra parameter '" . self::EXTRA_PARAM . "' is empty. Plugin is disabled.</error>",
                true);
        }
    }

    /**
     * @return mixed
     */
    public function getConfigFileNames()
    {
        return $this->configFileNames;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * * The method name to call (priority defaults to 0)
     * * An array composed of the method name to call and the priority
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset
     *
     * For instance:
     *
     * * array('eventName' => 'methodName')
     * * array('eventName' => array('methodName', $priority))
     * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        $result = [];
        /* subscribe to all available events  */
        $events = self::$config->getEventsEnabled();
        foreach ($events as $one) {
            if (isset(self::$EVENT_HANDLERS[$one])) {
                $result[$one] = self::$EVENT_HANDLERS[$one];
            }
        }
        return $result;
    }

    public function onCommand(
        \Composer\Script\Event $event
    ) {
        $this->_onEvent($event);
    }

    public function onInstaller(
        \Composer\Installer\InstallerEvent $event
    ) {
        $this->_onEvent($event);
    }

    public function onPackage(
        \Composer\Installer\PackageEvent $event
    ) {
        $this->_onEvent($event);
    }

    public function onPluginCommand(
        \Composer\Plugin\CommandEvent $event
    ) {
        $this->_onEvent($event);
    }

    public function onPluginInit(
        \Composer\EventDispatcher\Event $event
    ) {
        $this->_onEvent($event);
    }

    public function onPluginPreFileDownload(
        \Composer\Plugin\PreFileDownloadEvent $event
    ) {
        $this->_onEvent($event);
    }

    /**
     * Setup attribute on tests.
     *
     * @param \Praxigento\Composer\Plugin\Templates\Config $config
     */
    public function setConfig($config)
    {
        self::$config = $config;
    }

    /**
     * Setup attribute on tests.
     *
     * @param \Composer\IO\IOInterface $io
     */
    public function setIo(\Composer\IO\IOInterface $io)
    {
        $this->io = $io;
    }

}