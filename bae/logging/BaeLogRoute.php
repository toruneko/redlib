<?php

/**
 * @file: BaeLogRoute.php
 * @author: Toruneko<toruneko@outlook.com>
 * @date: 2014-4-9
 * @desc: SaeLogRoute class file.
 */
class BaeLogRoute extends CLogRoute
{

    protected function processLogs($logs)
    {
        foreach ($logs as $log) {
            $text = $this->formatLogMessage($log[0], $log[1], $log[2], $log[3]);

            $logger = BaeLog::getInstance(array(
                'user' => '77d93fa2c471405191d15571c02e508f',
                'passwd' => '9e7170398e164dfbb1bfeb5378269697',
            ));
            $logger->setLogLevel(16);
            switch($log[1]){
                case CLogger::LEVEL_INFO:
                    $logger->Notice($text);
                    break;
                case CLogger::LEVEL_ERROR:
                    $logger->Fatal($text);
                    break;
                case CLogger::LEVEL_PROFILE:
                    $logger->Debug($text);
                    break;
                case CLogger::LEVEL_TRACE:
                    $logger->Trace($text);
                    break;
                case CLogger::LEVEL_WARNING:
                    $logger->Warning($text);
                    break;
                default:
                    $logger->Fatal($text);
            }
        }
    }
}