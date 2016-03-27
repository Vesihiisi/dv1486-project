<?php

namespace Anax\TimeHelper;


/**
 * Convert timestamp to human-readable diff.
 * Source: http://php.net/manual/fr/datetime.diff.php#97880
 *
 */

class CTimeHelper
{

    public function friendlyTimeStamp($string)
    {
        $object = \DateTime::createFromFormat('Y-m-d H:i:s', $string);
        return $object->format("j M Y") . " at " . $object->format("G:i");
    }

    private function pluralize( $count, $text )
    {
        return $count . ( ( $count == 1 ) ? ( " $text" ) : ( " ${text}s" ) );
    }

    private function ago( $datetime )
    {
        $interval = date_create('now')->diff( $datetime );
        $suffix = ( $interval->invert ? ' ago' : '' );
        if ( $v = $interval->y >= 1 ) return $this->pluralize( $interval->y, 'year' ) . $suffix;
        if ( $v = $interval->m >= 1 ) return $this->pluralize( $interval->m, 'month' ) . $suffix;
        if ( $v = $interval->d >= 1 ) return $this->pluralize( $interval->d, 'day' ) . $suffix;
        if ( $v = $interval->h >= 1 ) return $this->pluralize( $interval->h, 'hour' ) . $suffix;
        if ( $v = $interval->i >= 1 ) return $this->pluralize( $interval->i, 'minute' ) . $suffix;
        return pluralize( $interval->s, 'second' ) . $suffix;
    }


    public function timeAgo($timestamp)
    {
        $timestamp = new \DateTime($timestamp);
        $now = new  \DateTime("now");
        $interval = date_diff($timestamp, $now);
        return $this->ago($timestamp);
    }
}
