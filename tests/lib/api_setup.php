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
 * Test Setup Trait for callbacklib_test.php and locallib_test.php.
 *
 * @package   assignsubmission_collabora
 * @copyright 2019 Benjamin Ellis, Synergy Learning, 2020 Justus Dieckmann WWU
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use assignsubmission_collabora\api\collabora_fs;
use mod_collabora\util as collabora_util;

/**
 * Test Setup Trait for callbacklib_test.php and locallib_test.php.
 *
 * @package   assignsubmission_collabora
 * @copyright 2019 Benjamin Ellis, Synergy Learning, 2020 Justus Dieckmann WWU
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait assignsubmission_collabora_test_api_setup {
    /**
     * Test Setup method.
     * @return array ($viewurl, $file, $fs, $assign, $plugin, $student)
     */
    public function setup_and_basic_tests_for_view_url() {
        global $CFG;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->create_instance($course);
        $plugin = $assign->get_submission_plugin_by_type('collabora');

        // Create a submission instance.
        // We need a user - a teacher.
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->setUser($teacher->id);

        $data = new \stdClass();
        // We use collabora::FORMAT_WORDPROCESSOR as example blank file.
        $data->assignsubmission_collabora_format   = collabora_util::FORMAT_WORDPROCESSOR;
        $data->assignsubmission_collabora_filename = 'test_handle_request';
        // Height never empty - required for all formats.
        $data->assignsubmission_collabora_height = 0;
        $this->assertTrue($plugin->save_settings($data));

        // Get Our initial file created above - which is an empty ods file.
        $fs    = get_file_storage();
        $files = $fs->get_area_files(
            $assign->get_context()->id,
            'assignsubmission_collabora',
            collabora_fs::FILEAREA_INITIAL,
            0, '', false, 0, 0, 1);
        $initialfile = reset($files);
        $this->assertNotEmpty($initialfile, 'No initial file created');

        // Get a student to make a submission.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($student->id);

        $newassignment = true;
        $submission    = $assign->get_user_submission($student->id, $newassignment);
        // We have to create the submission file - as per get_form_elements().
        $submissionfilerec = (object) [
            'contextid' => $initialfile->get_contextid(),
            'component' => $initialfile->get_component(),
            'filearea'  => collabora_fs::FILEAREA_SUBMIT,
            'itemid'    => $submission->id,
            'filepath'  => '/',
            'filename'  => $initialfile->get_filename(),
        ];
        // A copy of the empty ods file.
        $file = $fs->create_file_from_storedfile($submissionfilerec, $initialfile);
        $this->assertNotEmpty($file, 'No user submission file created');

        $data                   = new \stdClass();
        $data->submpathnamehash = $file->get_pathnamehash();
        $data->submfilename     = $file->get_filename();
        $data->subnewsubmssn    = $newassignment;
        $this->assertTrue($plugin->save($submission, $data));

        // Get the URL we need to call the editing.
        $collaborafs = new collabora_fs($student, $file);
        $viewurl     = $collaborafs->get_view_url();

        return [$viewurl->out(false), $file, $fs, $assign, $plugin, $student];
    }
}
