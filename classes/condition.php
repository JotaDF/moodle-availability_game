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

namespace availability_game;

defined('MOODLE_INTERNAL') || die();

/**
 * Condition main class.
 *
 * @package    availability_game
 * @copyright  2021 Jose Wilson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {

    /** @var int restrictlevel 0..15  */
    protected $restrictlevel = 0;

    /** @var int userlevel 0..15  */
    protected $userlevel = 0;

    /**
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data.
     */
    public function __construct($structure) {
        if (isset($structure->restrictlevel)) {
            $this->restrictlevel = $structure->restrictlevel;
        }
    }

    /**
     * Saves tree data back to a structure object.
     *
     * @return stdClass Structure object (ready to be made into JSON format)
     */
    public function save() {
        return (object) array('type' => 'game', 'restrictlevel' => $this->restrictlevel);
    }

    /**
     * @param int $restrictlevel default 0
     * @return stdClass Object representing condition
     */
    public static function get_json($restrictlevel = 0) {
        return (object) ['type' => 'game', 'restrictlevel' => $restrictlevel];
    }

    /**
     * @param bool $not Set true if we are inverting the condition
     * @param info $info Item we're checking
     * @param bool $grabthelot Performance hint: if true, caches information
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user)
     * @param int $userid User ID to check availability for
     * @return bool True if available
     */
    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        $available = false;

        if (!$userid || isguestuser($userid) || !\core_user::is_real_user($userid)) {
            return false;
        }

        if ($this->restrictlevel > 0) {
            $this->userlevel = $this->get_user_level($info->get_course()->id, $userid);
            $available = $this->userlevel >= $this->restrictlevel;
            if ($not) {
                $available = !$available;
            }
        }
        return $available;
    }

    /**
     * @param bool $full Set true if this is the 'full information' view
     * @param bool $not Set true if we are inverting the condition
     * @param info $info Item we're checking
     * @return string Information string (for admin) about all restrictions on
     *   this item
     */
    public function get_description($full, $not, \core_availability\info $info) {
        $message = get_string('levelnrequiredtoaccess', 'availability_game', $this->restrictlevel);
        if ($not) {
            $message = get_string('levelnnotrequiredtoaccess', 'availability_game', $this->restrictlevel);
        }
        return $message;
    }

    /**
     * Obtains a representation of the options of this condition as a string,
     * for debugging.
     *
     * @return string Text representation of parameters
     */
    protected function get_debug_string() {
        return 'game' . ':' . $this->restrictlevel;
    }

    /**
     * Return the current level of the user.
     *
     * @param  int $courseid The course ID.
     * @param  int $userid The user ID.
     * @return int The user level.
     */
    protected function get_user_level($courseid, $userid) {
        global $DB;
        if (!empty($userid) && !empty($courseid)) {
            $sql = 'SELECT level FROM {block_game} '
                    . 'WHERE userid=? AND courseid= ? ';
            $busca = $DB->get_record_sql($sql, [$userid, $courseid]);
            if (isset($busca->level)) {
                return $busca->level;
            } else {
                return 0;
            }
        }
        return 0;
    }

}
