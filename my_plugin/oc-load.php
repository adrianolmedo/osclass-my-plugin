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

// Model
require_once MY_PLUGIN_PATH . "model/MyPlugin.php";

// Helpers
require_once MY_PLUGIN_PATH . "helpers/hUtils.php";

// Controllers
require_once MY_PLUGIN_PATH . "controller/admin/crud.php";
require_once MY_PLUGIN_PATH . "controller/admin/settings.php";