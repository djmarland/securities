<?php

namespace SecuritiesService\Domain\Entity\Enum;

class AnnouncementStatus extends Enum
{
    const ERROR = 20;
    const NEW = 0;
    const DONE = -10;
    const LOW = -20;
}
