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
 * @package    availability_game
 * @copyright  2021 Jose Wilson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JavaScript for form editing week conditions.
 *
 * @module moodle-availability_game-form
 */
M.availability_game = M.availability_game || {}; // eslint-disable-line no-alert

/**
 * @class M.availability_game.form
 * @extends M.core_availability.plugin
 */
M.availability_game.form = Y.Object(M.core_availability.plugin);

/**
 * Groupings available for selection (alphabetical order).
 *
 * @property levels
 * @type Array
 */
M.availability_game.form.levels = null;

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} levelsfromstart Array of objects with .field, .display
 */
M.availability_game.form.initInner = function(levelsfromstart) {
    this.levels = levelsfromstart;
};

M.availability_game.form.getNode = function(json) {
    // Create HTML structure.
    var html = '<span class="availability-group"><label><label>' +
            M.util.get_string('conditiontitle', 'availability_game') + '</label> ' +
            '<select class="custom-select" name="field">' +
            '<option value="choose">' + M.util.get_string('choosedots', 'moodle') + '</option>';
    var fieldInfo;
    for (var i = 0; i < this.levels.length; i++) {
        fieldInfo = this.levels[i];
        // String has already been escaped using format_string.
        html += '<option value="l_' + fieldInfo.field + '">' + fieldInfo.display + '</option>';
    }
    html += '</select></label></span>';
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial values if specified.
    if (json.restrictlevel !== undefined &&
            node.one('select[name=field] > option[value=l_' + json.restrictlevel + ']')) {
        node.one('select[name=field]').set('value', 'l_' + json.restrictlevel);
    }

    // Add event handlers (first time only).
    if (!M.availability_game.form.addedEvents) {
        M.availability_game.form.addedEvents = true;
        var updateForm = function(input) {
            var ancestorNode = input.ancestor('span.availability_game');
            var op = ancestorNode.one('select[name=op]');
            var novalue = (op.get('value') === 'isempty' || op.get('value') === 'isnotempty');
            ancestorNode.one('input[name=value]').set('disabled', novalue);
            M.core_availability.form.update();
        };
        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('change', function() {
             updateForm(this);
        }, '.availability_game select');
        root.delegate('change', function() {
             updateForm(this);
        }, '.availability_game input[name=value]');
    }

    return node;
};

M.availability_game.form.fillValue = function(value, node) {
    // Set field.
    var field = node.one('select[name=field]').get('value');
    if (field.substr(0, 3) === 'l_') {
        value.restrictlevel = field.substr(3);
    }
};
