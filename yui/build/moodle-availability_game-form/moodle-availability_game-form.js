YUI.add('moodle-availability_game-form', function (Y, NAME) {

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
 * Condition main class.
 *
 * @package    availability_game
 * @copyright  2021 Jose Wilson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JavaScript for form editing level game conditions.
 *
 * @module moodle-availability_game-form
 */
M.availability_game = M.availability_game || {}; // eslint-disable-line

/**
 * @class M.availability_game.form
 * @extends M.core_availability.plugin
 */
M.availability_game.form = Y.Object(M.core_availability.plugin);

/**
 * Groups available for selection (alphabetical order).
 *
 * @property levels
 * @type Array
 */
M.availability_game.form.levels = null;

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} levels Array of objects containing level
 */
M.availability_game.form.initInner = function (levels) {
    this.levels = levels;
};

M.availability_game.form.getNode = function (json) {
    // Create HTML structure.
    var html = '<label><span class="pr-3">' + M.util.get_string('conditiontitle', 'availability_game') + '</span> ' +
            '<span class="availability_game">' +
            '<select name="level" class="custom-select">' +
            '<option value="choose">' + M.util.get_string('choosedots', 'moodle') + '</option>';
    for (var i = 0; i < this.levels.length; i++) {
        var level = this.levels[i];
        // String has already been escaped using format_string.
        html += '<option value="' + level.level + '">' + level.name + '</option>';
    }
    html += '</select></span></label>';
    var node = Y.Node.create('<span class="form-inline">' + html + '</span>');

    // Set initial values (leave default 'choose' if creating afresh).
    if (typeof json.restrictlevel !== 'undefined') {
        node.one('select[name=level]').set('value', '' + json.restrictlevel);
    } else if (typeof json.restrictlevel === 'undefined') {
        node.one('select[name=level]').set('value', 'choose');
    }

    // When any select chances.
    Y.one('#fitem_id_availabilityconditionsjson, .availability-field').delegate('change', function () {
        M.core_availability.form.update();
    }, '.availability_game select');
    return node;
};

M.availability_game.form.fillValue = function (value, node) {
    var selected = node.one('select[name=level]').get('value');
    if (selected !== 'choose') {
        value.restrictlevel = parseInt(selected, 10);
    }
};

M.availability_game.form.fillErrors = function (errors, node) {
    var selected = node.one('select[name=level]').get('value');
    // Check level item level.
    if (selected && selected === 'choose') {
        errors.push('availability_game:error_selectlevel');
    }
};


}, '@VERSION@', {"requires": ["base", "node", "event", "io", "moodle-core_availability-form"]});
