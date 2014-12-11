<?php

/*
 * This file is a part of the ConsoleApplication library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleApplication\DependencyInjection\Loader;

use ConsoleApplication\DependencyInjection\Bag\BagInterface;
use ConsoleApplication\Exception\ConfigurationException;
use ConsoleApplication\Exception\FileNotFoundException;

interface FileLoaderInterface
{
    /*------------------------------------------------------------------------*\
    | Public methods                                                           |
    \*------------------------------------------------------------------------*/

    /**
     * Loads a file configuration to a bag
     *
     * @param string       $filename
     * @param string       $root
     * @param BagInterface $bag
     * @param boolean      $recursive
     * @param boolean      $suppressException
     *
     * @return BagInterface
     *
     * @throws ConfigurationException
     * @throws FileNotFoundException
     */
    public function loadFileToBag($filename, $root, BagInterface $bag, $recursive = false, $suppressException = false);

    /**
     * Resolves parameters in a bag using another bag of parameters
     *
     * @param BagInterface $bag
     * @param BagInterface $parameters
     *
     * @return BagInterface
     *
     * @throws ConfigurationException
     */
    public function resolveParameters(BagInterface $bag, BagInterface $parameters);
}
