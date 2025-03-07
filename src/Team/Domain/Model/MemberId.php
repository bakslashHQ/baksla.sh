<?php

declare(strict_types=1);

namespace App\Team\Domain\Model;

enum MemberId: string
{
    case ArnaudDeAbreu = 'arnaud-de-abreu';
    case FelixEymonot = 'felix-eymonot';
    case HugoAlliaume = 'hugo-alliaume';
    case JeremyRomey = 'jeremy-romey';
    case JulesPietri = 'jules-pietri';
    case MathiasArlaud = 'mathias-arlaud';
    case RobinChalas = 'robin-chalas';
    case ValmontPehautPietri = 'valmont-pehaut-pietri';
    case YazidHassani = 'yazid-hassani';
}
