<?php

declare(strict_types=1);

namespace App\Lib\Csp;


interface Command
{
    /**
     * Утилита cryptcp
     */
    public const CPROCSP = '/opt/cprocsp/bin/amd64/cryptcp';

    /**
     * Утилита certmgr
     */
    public const CERTMGR = '/opt/cprocsp/bin/amd64/certmgr';
}
