<?php

/**
 * Image presets for <x-image>: the longer sides offered in the srcset,
 * smallest first. Each is scaled to fit inside size x size, never up.
 */
return [
  'presets' => [
    'large' => [900, 1200, 2400],
    'preview' => [900, 1600],
    'teaser' => [1600, 2400],
    'home' => [1200, 2000],
  ],
];
