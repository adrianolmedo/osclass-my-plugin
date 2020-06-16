<?php
/*
 * My Plugin - It's a basic plugin for Osclass as resource for a tutorial about how to implement it.
 * Copyright (C) 2020  Adrián Olmedo
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

// Example of plugin helper
if (!function_exists('my_plugin_helper')) {
	function my_plugin_helper() {
		return true;
	}
}

/**
 * Get the current date or the sum depending of her parameters.
 *
 * - Example of use 1: echo todaydate();            // Show the current date.
 * - Example of use 2: echo todaydate(3, 'days');   // Show the current date but sum 3 days.
 * - Example of use 3: echo todaydate(3, 'years');  // Show the current date but sum 3 years.
 *
 * @param string $time The default value is H:i:s but can change during the estatemnt of function.
 * @param int $num It defined null at the start as it can be empty, on the contrary must have a integer numeric value.
 * @param string $ymd It defined null at the start as ir can be empty, on the contrary so it specific 'days', 'years' or 'month'.
 * @return string $date Return the current in the H:i:s format.
 * @return string $dateplus Return the current date added.
 */
if (!function_exists('my_plugin_todaydate')) {
    function my_plugin_todaydate($num = null, $ymd = null, $time = 'H:i:s') {
        $date = date('Y-m-d '.$time);

        if ($num && $ymd) {
            $dateplus = strtotime('+'.$num.' '.$ymd, strtotime($date));
            $dateplus = date('Y-m-d H:i:s', $dateplus);
            return $dateplus;
        } else {
            return $date;
        }
    }
}

/**
 * Make a text string be a valid URI address (include mailto).
 * 
 * - Example of use 1: echo setURL("name@email.com");           // Show mailto:name@email.com
 * - Example of use 2: echo setURL("mailto:name@email.com");    // Show mailto:name@email.com
 * - Example of use 3: echo setURL("websitename.com");          // Show http://websitename.com
 * - Example of use 4: echo setURL("http://websitename.com");   // Show http://websitename.com
 * - Example of use 5: echo setURL("https://websitename.com");  // Show https://websitename.com
 *
 * @param string $url
 * @return string
 */
if (!function_exists('my_plugin_setURL')) {
    function my_plugin_setURL($url) {
        $allowed = ['mailto'];
        $parsed = parse_url($url);
        if (in_array($parsed['scheme'], $allowed)) {
            return $url;

        } elseif (preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $url)) {
        return 'mailto:'.$url;

        // without localhost '/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'
        // with localhost '/^(http|https):\/\/+(localhost|[A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'
        } elseif (preg_match('/^((http|https):\/\/?)[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/?))/i', $url)) {
            return $url;

        } else {
            return 'http://'.$url;
        }
    }
}