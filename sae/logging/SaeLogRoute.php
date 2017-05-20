<?php

/**
 * @file: SaeLogRoute.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: SaeLogRoute class file.
 */
class SaeLogRoute extends CLogRoute
{

    protected function processLogs($logs)
    {
        $text = '';
        foreach ($logs as $log) {
            $text .= $this->formatLogMessage($log[0], $log[1], $log[2], $log[3]);
        }

        @sae_debug($text);
    }
}