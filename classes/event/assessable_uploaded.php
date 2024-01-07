<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The assignsubmission_collabora assessable uploaded event.
 *
 * @package assignsubmission_collabora
 * @copyright 2019 Benjamin Ellis, Synergy Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_collabora\event;

/**
 * The assignsubmission_collabora assessable uploaded event class.
 *
 * @package assignsubmission_collabora
 * @copyright 2019 Benjamin Ellis, Synergy Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assessable_uploaded extends \core\event\assessable_uploaded {
    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has uploaded a file to the submission with id '$this->objectid' " .
            "in the assignment activity with course module id '$this->contextinstanceid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventassessableuploaded', 'assignsubmission_collabora');
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/assign/view.php', [
            'id' => $this->contextinstanceid,
        ]);
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        parent::init();
        $this->data['objecttable'] = 'assign_submission';
    }

    /**
     * Object mappings for assessable uploaded event.
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return [
            'db'      => 'assign_submission',
            'restore' => 'submission',
        ];
    }
}
