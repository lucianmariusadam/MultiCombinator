<?php

/**
 * Lucian Marius Adam
 *
 * @license
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 lucianmariusadam
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author  Lucian Marius Adam <lucianmariusadam@gmail.com>
 */

namespace LucianMariusAdam\MultiCombinator;

/**
 * Class MultiCombinator
 * @author   Lucian Marius Adam <lucianmariusadam@gmail.com>
 * @see      https://github.com/lucianmariusadam/MultiCombinator
 */
class MultiCombinator implements \Iterator
{
    protected $lists = null;

    protected $list_count = null;
    protected $original_keys = null;
    protected $use_original_keys = true;
    protected $pos = null;
    protected $index = null;


    /**
     * Constructor
     * @param null $lists
     */
    function __construct( $lists = null )
    {
        $this->setLists( $lists );
    }


    /**
     * Set lists of values to combine
     * @param array|null $lists
     *
     * @return bool
     */
    public function setLists( $lists )
    {
        $result = true;

        if ( !is_array( $lists ) )
        {
            $lists = [ ];
        }

        $empty_list = false;

        if ( empty( $lists ) )
        {
            $empty_list = true;
        }

        foreach ( $lists as $key => $list )
        {
            $list = (array) $list;

            if ( empty( $list ) )
            {
                $empty_list = true;
                break;
            }
        }

        if ( $empty_list )
        {
            $result = false;
        }
        else
        {
            $this->lists         = array_values( $lists );
            $this->list_count    = count( $this->lists );
            $this->original_keys = array_keys( $lists );

            $this->rewind();
        }

        return $result;
    }


    /**
     * Get index of current combination
     * @return null
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Return the key of current combination
     * @return null
     */
    function key()
    {
        return $this->pos;
    }


    /**
     * Get current combination
     * @return array|null
     */
    function current()
    {
        $r = null;

        if ( $this->pos !== null )
        {
            $r = [ ];

            for ( $i = 0; $i < $this->list_count; $i ++ )
            {
                $r[] = $this->lists [ $i ][ $this->pos [ $i ] ];
            }

            if ( $this->use_original_keys )
            {
                $r = array_combine( $this->original_keys, $r );
            }
        }

        return $r;
    }


    /**
     * Advance to next combination
     * @return array|null
     */
    public function next()
    {
        // we have lists
        if ( $this->list_count > 0 )
        {
            $list  = $this->list_count - 1;
            $pos   = $this->pos;
            $index = $this->index;

            // first iteration
            if ( $pos === null )
            {
                $pos   = array_fill( 0, $this->list_count, 0 );
                $index = 0;
            }
            else
            {
                while ( $list >= 0 && ++ $pos [ $list ] > count( $this->lists[ $list ] ) - 1 )
                {
                    $pos [ $list ] = 0;
                    $list --;
                }

                $index ++;

                if ( $list < 0 )
                {
                    $pos   = null;
                    $index = null;
                }
            }

            $this->pos   = $pos;
            $this->index = $index;

            return $this->current();
        }
    }


    /**
     * Rewind iterator
     * @return array|null
     */
    function rewind()
    {
        $this->pos = null;
        $this->index = null;

        return $this->current();
    }


    /**
     * Check if current position is valid
     * @return bool
     */
    public function valid()
    {
        return $this->pos_is_valid( $this->pos );
    }


    /**
     * Check a position for validity
     * @param $pos
     *
     * @return bool
     */
    function pos_is_valid( $pos )
    {
        $result = true;

        $n = min( count( $pos ), $this->list_count );

        for ( $i = 0; $i < $n; $i ++ )
        {
            if ( $pos [ $i ] > count( $this->lists[ $i ] ) - 1 )
            {
                $result = false;
                break;
            }
        }

        return $result;
    }


    /**
     * Seek to given combination
     * @param $pos
     *
     * @return array|bool|null
     */
    public function seek( $pos )
    {
        $result = false;

        if ( $this->pos_is_valid ( $pos ) )
        {
            $n = min( count( $pos ), $this->list_count );

            for ( $i = 0; $i < $n; $i ++ )
            {
                if ( $pos [ $i ] !== null )
                {
                    $this->pos[ $i ] = $pos [ $i ];
                }
            }

            $result = $this->current();
        }

        return $result;
    }


    /**
     * Use original keys
     * @param null $state
     *
     * @return bool
     */
    public function use_original_keys( $state = null )
    {
        if ( !is_null( $state ) )
        {
            $this->use_original_keys = (bool) $state;
        }

        return $this->use_original_keys;
    }


    /**
     * Get lists of values
     * @return null
     */
    public function getLists()
    {
        return $this->lists;
    }


    /**
     * Get count of possible combinations
     * @return int
     */
    public function count()
    {
        $result = 0;

        if ( $this->list_count )
        {
            $result = 1;

            foreach ($this->lists as $list)
            {
                $result *= count( $list );
            }
        }

        return $result;
    }


    /**
     * Get all combinations
     * @return array
     */
    public function getAll()
    {
        $this->rewind();

        $result = [];

        while ( $combination = $this->next() )
        {
            $result [] = $combination;
        }

        return $result;
    }
}