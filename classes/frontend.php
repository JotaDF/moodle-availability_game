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
 * Frontend file.
 *
 * @package    availability_game
 * @copyright  2021 Jose Wilson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_game;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/blocklib.php');

/**
 * Frontend class.
 *
 * @package    availability_game
 * @copyright  2021 Jose Wilson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {

    /**
     * Decides whether this plugin should be available in a given course. The
     * plugin can do this depending on course or system settings.
     *
     * @param stdClass $course Course object
     * @param \cm_info $cm Course-module currently being edited (null if none)
     * @param \section_info $section Section currently being edited (null if none)
     * @return bool False when adding is disabled.
     */
    protected function allow_add($course, \cm_info $cm = null, \section_info $section = null) {
        global $DB;
        $coursecontext = \context_course::instance($course->id);
        $blockrecords = $DB->get_records('block_instances', array('blockname' => 'game', 'parentcontextid' => $coursecontext->id));
        $count = 0;
        foreach ($blockrecords as $b) {
            $count++;
        }
        return $count > 0;
    }

    /**
     * Get javascript init params.
     *
     * @return mixed the level time spent in seconds
     */
    protected function get_javascript_strings() {
        return array('level', 'conditiontitle');
    }

    /**
     * Get javascript init params.
     *
     * @param \stdClass $course
     * @param \cm_info $cm
     * @param \section_info $section
     * @return mixed the levels
     */
    protected function get_javascript_init_params($course, \cm_info $cm = null, \section_info $section = null) {
        global $DB;
        $coursecontext = \context_course::instance($course->id);
        $blockrecords = $DB->get_records('block_instances', array('blockname' => 'game', 'parentcontextid' => $coursecontext->id));
        foreach ($blockrecords as $b) {
            $blockinstance = \block_instance('game', $b);
        }
        $levelarray = array();
        if (isset($blockinstance->config->level_number)) {
            for ($i = 1; $i <= $blockinstance->config->level_number; $i++) {
                $levelarray[] = (object)array('id' => $i, 'name' => get_string('label_level', 'block_game') . ' '. $i);
                //$levelarray[$i] = $i;
            }
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $levelarray[] = (object)array('id' => $i, 'name' => get_string('label_level', 'block_game') . ' '. $i);
                //$levelarray[$i] = $i;
            }
        }
        return(array($levelarray));
    }

}
