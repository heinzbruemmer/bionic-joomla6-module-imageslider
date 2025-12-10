<?php

/**
 * @package     Bionic.Module.ImageSlider
 * @copyright   (C) 2025 Bionic Laboratories BLG GmbH
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Uri\Uri;

// Bilder sammeln
$images = [];
for ($i = 1; $i <= 5; $i++) {
    $imagePath = $params->get("image{$i}", '');
    if (!empty($imagePath)) {
        $images[] = [
            'src'   => Uri::root() . $imagePath,
            'link'  => $params->get("image{$i}_link", ''),
            'alt'   => $params->get("image{$i}_alt", "Bild {$i}"),
            'title' => $params->get("image{$i}_title", ''),
        ];
    }
}

// Keine Bilder? Nichts anzeigen
if (empty($images)) {
    return;
}

// Eindeutige Slider-ID für dieses Modul
$sliderId = 'bionic-mod-slider-' . $module->id;

// Template laden
require ModuleHelper::getLayoutPath('mod_imageslider', $params->get('layout', 'default'));
