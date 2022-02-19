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
 * This file contains the Activity modules block.
 *
 * @package    course_activity_list
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/lib.php');
class block_course_activity_list extends block_list {
    public function init() {
        $this->title = get_string('activity_lists', 'block_course_activity_list');
    }

    public function get_content() {
        global $COURSE, $CFG, $USER, $PAGE;
        $courseid = $COURSE->id;
        $context = context_course::instance($courseid);
        $roles = get_user_roles($context, $USER->id, true);
        $role = key($roles);
        if (isloggedin()){
                    $activityname = '';
                    $modinfo = get_fast_modinfo($courseid);
                    $this->content = new stdClass();
                    $this->content->items = array();
                    $this->content->icons = array();
                    $this->content->footer = "";
                    foreach ($modinfo->cms as $cm) {
                        $coursemod = $modinfo->get_cm($cm->id);
                        if (!$cm->uservisible or ! $cm->has_view()) {
                            continue;
                        }
                        if ($coursemod->name == 'label') {
                            continue;
                        }
                        if ($coursemod->completion === 0 || empty($coursemod->completion) || $coursemod->completion == NULL){
                            $cm_completed = '-';
                        } else {
                            $cm_completed = 'Completed';
                        }
                        $url = new moodle_url($CFG->wwwroot . '/mod/' . $coursemod->modname . '/view.php', array('id' => $coursemod->id));
                        $activityname = $coursemod->id . ' - ' . $coursemod->name . ' - ' . date('d-M-Y',($coursemod->added/100)) . '  ' . $cm_completed;
                        $this->content->items[] = html_writer::link($url, $activityname);
                    }

            if (empty($this->content->items)) {
                $this->content->items[] = get_string('activitynotfound', 'block_course_activity_list');
            }
        return $this->content;
    }
    }
    public function applicable_formats(){
        return array(
            'course-view' => true,
            'course-view-social' => false
        );
    }
    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('activity_lists', 'block_course_activity_list');
            } else {
                $this->title = $this->config->title;
            }
            if (empty($this->config->text)) {
                $this->config->text = get_string('activity_lists', 'block_course_activity_list');
            }
        }
    }
}