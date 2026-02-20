<?php

namespace Itsmurumba\Hostpinnacle\Exceptions;

use Exception;

/**
 * Exception thrown when a required value is null (e.g. missing msg or mobile for SMS).
 */
class IsNullException extends Exception {}
