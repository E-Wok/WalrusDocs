<?php

namespace app\engine\controllers;

use Walrus\core\WalrusController;
use Walrus\core\WalrusHelpers;

/**
 * Class HomeController
 * @package engine\controllers
 */
class HomeController extends WalrusController
{
    private $versions = array(
        '0.9.0b',
        'default' => '1.0.0'
    );

    public function run()
    {
        $this->skeleton('_skeleton_main');
    }

    public function doc($version = false, $doc = false)
    {

        if (!$doc && $version && !is_numeric(substr($version, 0, 1))) {
            $doc = $version;
            $version = $this->versions['default'];
        }

        // control
        $version = $version && in_array($version, $this->versions) ? $version : $this->versions['default'];
        $isDefault = $this->versions['default'] === $version;
        $doc = $doc ?: ($version == $this->versions[0] ? 'required' : 'change-log');
        $url = $_ENV['W']['base_url'] . 'doc/' . ($isDefault ? '' : $version . '/');

        // meta vars
        $description = $this->getSoft('home', 'loadMeta', array($doc, $version, $isDefault));
        $this->register('description', $description);
        $this->register('title', ucwords(str_replace('-', ' ', $doc)));

        // view
        $this->skeleton('_skeleton_doc');
        $documentation = $this->getSoft('home', 'loadDoc', array($doc, $version, $isDefault, $url));

        // content vars
        $this->register('documentation', $documentation);
        $this->register('isDefault', $isDefault);
        $this->register('url', $url);
        $this->register('versions', $this->versions);
        $this->register('current_version', $version);
    }

    public function loadMeta($doc, $version, $isDefault)
    {
        $this->register('doc', $doc);
        if ($isDefault) {
            $this->setView('/doc/meta');
        } else {
            $this->setView('version/' . $version . '/doc/meta');
        }
    }

    public function loadDoc($doc, $version, $isDefault, $url)
    {
        $this->register('url', $url);

        if ($isDefault) {
            $this->setView('/doc/' . $doc);
        } else {
            $this->setView('version/' . $version . '/doc/' . $doc);
        }
    }
}
