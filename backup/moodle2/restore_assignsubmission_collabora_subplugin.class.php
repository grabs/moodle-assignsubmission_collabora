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
 * This file contains the class for restore of this submission plugin.
 *
 * @package assignsubmission_collabora
 * @copyright 2019 Synergy Learning {@link https://www.synergy-learning.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Restore subplugin class.
 *
 * Provides the necessary information
 * needed to restore one assign_submission subplugin.
 *
 * @package assignsubmission_collabora
 * @copyright 2019 Synergy Learning {@link https://www.synergy-learning.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_assignsubmission_collabora_subplugin extends restore_subplugin {
    /**
     * Returns the paths to be handled by the subplugin at workshop level.
     * @return array
     */
    protected function define_submission_subplugin_structure() {
        $paths = [];

        $elename = $this->get_namefor('submission');
        $elepath = $this->get_pathfor('/submission_collabora');
        // We used get_recommended_name() so this works.
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths;
    }

    /**
     * Processes one submission_collabora element.
     * @param  mixed $data
     * @return void
     */
    public function process_assignsubmission_collabora_submission($data) {
        global $DB;

        $data             = (object) $data;
        $data->assignment = $this->get_new_parentid('assign');
        $oldsubmissionid  = $data->submission;
        // The mapping is set in the restore for the core assign activity
        // when a submission node is processed.
        $data->submission = $this->get_mappingid('submission', $data->submission);

        $DB->insert_record('assignsubmission_collabora', $data);

        $this->add_related_files('assignsubmission_collabora',
            \assignsubmission_collabora\api\collabora_fs::FILEAREA_SUBMIT,
            'submission',
            null,
            $oldsubmissionid);
    }
}
