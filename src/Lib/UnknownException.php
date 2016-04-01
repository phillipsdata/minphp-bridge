<?php

/**
 * Backwards Compatible UnknownException replacement
 */
class UnknownException extends ErrorException
{

    /**
     * Placeholder for backwards compatbility
     *
     * @param int $err_no
     * @param string $err_str
     * @param string $err_file
     * @param in $err_line
     */
    public static function setErrorHandler($err_no, $err_str, $err_file, $err_line)
    {
        // unused
    }

    /**
     * Placeholder for backwards compatbility
     *
     * @param Exception $e
     */
    public static function setExceptionHandler(Exception $e)
    {
        // unused
    }

    /**
     * Placeholder for backwards compatbility
     */
    public static function setFatalErrorHandler()
    {
        // unused
    }
}
