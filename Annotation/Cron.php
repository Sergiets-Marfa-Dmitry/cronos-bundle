<?php

namespace MyBuilder\Bundle\CronosBundle\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Cron annotation which we can parse to generate a cron file
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target("CLASS")
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class Cron
{
    public string $minute;
    public string $hour;
    public ?string $dayOfMonth = null;
    public ?string $month = null;
    public ?string $dayOfWeek = null;
    public ?string $comment = null;
    public ?string $logFile = null;
    public ?string $errorFile = null;

    // If true add /dev/null.
    public ?bool $noLogs = null;

    // Which server should this cron job run on.
    public string $server;

    public ?string $params = null;
    public ?string $executor = null;

    public function __construct(string  $minute,
                                string $hour,
                                ?string $server,
                                ?string $dayOfMonth = null,
                                ?string $dayOfWeek = null,
                                ?string $comment = null,
                                ?string $logFile = null,
                                ?string $errorFile = null,
                                ?bool $noLogs = null,
                                ?string $params = null,
                                ?string $executor = null)
    {
        $this->minute = $minute;
        $this->hour   = $hour;
        $this->server   = $server;
        $this->dayOfMonth   = $dayOfMonth;
        $this->dayOfWeek   = $dayOfWeek;
        $this->comment   = $comment;
        $this->logFile   = $logFile;
        $this->errorFile   = $errorFile;
        $this->noLogs   = $noLogs;
        $this->params   = $params;
        $this->executor   = $executor;
    }
}