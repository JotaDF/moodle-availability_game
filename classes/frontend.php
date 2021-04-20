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
                $levelarray[$i] = $i;
            }
        } else {
            $levelarray = array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6',
                '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12');
        }
        return(array(self::convert_associative_array_for_js($levelarray, 'field', 'display')));
    }

}
