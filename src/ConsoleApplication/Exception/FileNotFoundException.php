<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\Exception;

class FileNotFoundException extends RuntimeException
{
    /*------------------------------------------------------------------------*\
    | Public static methods                                                    |
    \*------------------------------------------------------------------------*/

    /**
     * Configuration file not found exception
     *
     * @param string $filename
     *
     * @return FileNotFoundException
     */
    public static function configurationFileNotFoundException($filename)
    {
        return new static(sprintf('Configuration file "%s" not found.', $filename));
    }
}
