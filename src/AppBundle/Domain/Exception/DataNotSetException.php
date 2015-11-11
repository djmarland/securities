<?php

namespace AppBundle\Domain\Exception;

/**
 * Class DataNotSetException
 * For use when trying to call data that had not been
 * set by a previous action
 */
class DataNotSetException extends \Exception
{
}
