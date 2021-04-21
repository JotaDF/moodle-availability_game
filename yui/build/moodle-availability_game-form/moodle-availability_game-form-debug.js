YUI.add('moodle-availability_game-form', function (Y, NAME) {

/**
 * JavaScript for form editing level game conditions.
 *
 * @module moodle-availability_game-form
 */
M.availability_game = M.availability_game || {};

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
M.availability_game.form.initInner = function(levels) {
    this.levels = levels;
};

M.availability_game.form.getNode = function(json) {
    // Create HTML structure.
    var html = '<label><span class="pr-3">' + M.util.get_string('title', 'availability_game') + '</span> ' +
            '<span class="availability-game">' +
            '<select name="id" class="custom-select">' +
            '<option value="choose">' + M.util.get_string('choosedots', 'moodle') + '</option>';
    for (var i = 0; i < this.levels.length; i++) {
        var level = this.levels[i];
        // String has already been escaped using format_string.
        html += '<option value="' + level.id + '">' + level.name + '</option>';
    }
    html += '</select></span></label>';
    var node = Y.Node.create('<span class="form-inline">' + html + '</span>');

    // Set initial values (leave default 'choose' if creating afresh).
    if (json.creating === undefined) {
        if (json.id !== undefined &&
                node.one('select[name=id] > option[value=' + json.id + ']')) {
            node.one('select[name=id]').set('value', '' + json.id);
        }
    }

    // Add event handlers (first time only).
    if (!M.availability_game.form.addedEvents) {
        M.availability_game.form.addedEvents = true;
        var root = Y.one('.availability-field');
        root.delegate('change', function() {
            // Just update the form fields.
            M.core_availability.form.update();
        }, '.availability_game select');
    }

    return node;
};

M.availability_game.form.fillValue = function(value, node) {
    var selected = node.one('select[name=id]').get('value');
    if (selected === 'choose') {
        value.restrictlevel = 'choose';
    } else {
        value.restrictlevel = parseInt(selected, 10);
    }
};

M.availability_game.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);
    // Check level item id.
    if (value.id && value.id === 'choose') {
        errors.push('availability_game:error_selectlevel');
    }
};


}, '@VERSION@', {"requires": ["base", "node", "event", "moodle-core_availability-form"]});
