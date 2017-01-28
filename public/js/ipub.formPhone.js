/**
 * ipub.formPhone.js
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        iPublikuj:FormPhone!
 * @subpackage     java-script
 * @since          1.0.0
 *
 * @date           19.12.15
 */

/**
 * Client-side script for iPublikuj:FormPhone!
 *
 * @author        Adam Kadlec <adam.kadlec@fastybird.com>
 * @package       iPublikuj:FormPhone!
 * @version       1.0.0
 *
 * @param {jQuery} $ (version > 1.7)
 * @param {Window} window
 * @param {Document} document
 * @param {Location} location
 * @param {Navigator} navigator
 */
;(function ($, window, document, location, navigator) {
    /* jshint laxbreak: true, expr: true */
    "use strict";

    var IPub = window.IPub || {};

    IPub.Forms = IPub.Forms || {};

    /**
     * Forms phone extension definition
     *
     * @param {jQuery} $element
     * @param {Object} options
     */
    IPub.Forms.Phone = function ($element, options)
    {
        this.$element = $element;

        this.name = this.$element.prop('id');
        this.options = $.extend($.fn.ipubFormsPhone.defaults, options, this.$element.data('settings') || {});

        this.isIphone = (window.orientation !== undefined);
        this.isAndroid = navigator.userAgent.toLowerCase().indexOf("android") > -1;
        this.isIE = window.navigator.appName == 'Microsoft Internet Explorer';
    };

    IPub.Forms.Phone.prototype =
    {
        // Initial function.
        init: function ()
        {
            if (this.isAndroid) return; // No support because caret positioning doesn't work on Android

            var that = this;

            this.mask = String(this.$element.find(":selected").data('mask'));
            this.$phoneField = $(document.getElementsByName(this.options.field)[0]);

            this.$element.bind('change.ipub.forms.phone', function () {
                that.mask = String(that.$element.find(":selected").data('mask'));

                that.attach();

                // Perform initial check for existing values
                that.checkVal();
            });

            this.attach();
            this.listen();

            // Perform initial check for existing values
            this.checkVal();
        },

        attach: function ()
        {
            var defs = this.options.definitions;
            var len = this.mask.length;

            this.tests = new Array;
            this.partialPosition = this.mask.length;
            this.firstNonMaskPos = null;

            $.each(this.mask.split(''), $.proxy(function (i, c) {
                if (c == '?') {
                    len--;
                    this.partialPosition = i;

                } else if (defs[c]) {
                    this.tests.push(new RegExp(defs[c]));

                    if (this.firstNonMaskPos === null) {
                        this.firstNonMaskPos = this.tests.length - 1;
                    }

                } else {
                    this.tests.push(null);
                }

            }, this));

            this.buffer = $.map(this.mask.split(''), $.proxy(function (c, i) {
                if (c != '?') return defs[c] ? this.options.placeholder : c;
            }, this));

            this.focusText = this.$phoneField.val();

            this.$phoneField.data('rawMaskFn', $.proxy(function () {
                return $.map(this.buffer, function (c, i) {
                    return this.tests[i] && c != this.options.placeholder ? c : null;
                }).join('');
            }, this))
        },

        listen: function ()
        {
            if (this.$phoneField.attr('readonly'))
                return;

            var pasteEventName = (this.isIE ? 'paste' : 'input') + '.ipub.forms.phone';

            this.$phoneField
                .off('unmask.ipub.forms.phone')
                .on('unmask.ipub.forms.phone', $.proxy(this.unmask, this))

                .off('focus.ipub.forms.phone')
                .on('focus.ipub.forms.phone', $.proxy(this.focusEvent, this))
                .off('blur.ipub.forms.phone')
                .on('blur.ipub.forms.phone', $.proxy(this.blurEvent, this))

                .off('keydown.ipub.forms.phone')
                .on('keydown.ipub.forms.phone', $.proxy(this.keydownEvent, this))
                .off('keypress.ipub.forms.phone')
                .on('keypress.ipub.forms.phone', $.proxy(this.keypressEvent, this))

                .off(pasteEventName)
                .on(pasteEventName, $.proxy(this.pasteEvent, this));
        },

        checkVal: function (allow)
        {
            var len = this.mask.length;

            // Try to place characters where they belong
            var test = this.$phoneField.val();
            var lastMatch = -1;

            for (var i = 0, pos = 0; i < len; i++) {
                if (this.tests[i]) {
                    this.buffer[i] = this.options.placeholder;

                    while (pos++ < test.length) {
                        var c = test.charAt(pos - 1);

                        if (this.tests[i].test(c)) {
                            this.buffer[i] = c;
                            lastMatch = i;

                            break
                        }
                    }

                    if (pos > test.length) {
                        break
                    }

                } else if (this.buffer[i] == test.charAt(pos) && i != this.partialPosition) {
                    pos++;
                    lastMatch = i;
                }
            }

            if (!allow && lastMatch + 1 < this.partialPosition) {
                this.$phoneField.val('');
                this.clearBuffer(0, len);

            } else if (allow || lastMatch + 1 >= this.partialPosition) {
                this.writeBuffer();

                if (!allow) {
                    this.$phoneField.val(this.$phoneField.val().substring(0, lastMatch + 1));
                }
            }

            return (this.partialPosition ? i : this.firstNonMaskPos)
        },

        // Helper Function for Caret positioning
        caret: function (begin, end)
        {
            if (this.$phoneField.length === 0) return;

            if (typeof begin == 'number') {
                end = (typeof end == 'number') ? end : begin;

                return this.$phoneField.each(function () {
                    if (this.setSelectionRange) {
                        this.setSelectionRange(begin, end);

                    } else if (this.createTextRange) {
                        var range = this.createTextRange();
                        range.collapse(true);
                        range.moveEnd('character', end);
                        range.moveStart('character', begin);
                        range.select();
                    }
                });

            } else {
                if (this.$phoneField[0].setSelectionRange) {
                    begin = this.$phoneField[0].selectionStart;
                    end = this.$phoneField[0].selectionEnd;

                } else if (document.selection && document.selection.createRange) {
                    var range = document.selection.createRange();

                    begin = 0 - range.duplicate().moveStart('character', -100000);
                    end = begin + range.text.length;
                }

                return {
                    begin: begin,
                    end: end
                }
            }
        },

        unmask: function ()
        {
            this.$phoneField
                .unbind('.ipub.forms.phone')
                .removeData('ipub.forms.phone');
        },

        focusEvent: function ()
        {
            this.focusText = this.$phoneField.val();
            var len = this.mask.length;
            var pos = this.checkVal();

            this.writeBuffer()

            var that = this

            var moveCaret = function () {
                if (pos == len) {
                    that.caret(0, pos);

                } else {
                    that.caret(pos)
                }
            }

            moveCaret()
            setTimeout(moveCaret, 50)
        },

        blurEvent: function ()
        {
            this.checkVal();

            if (this.$phoneField.val() !== this.focusText) {
                this.$phoneField.trigger('change');
                this.$phoneField.trigger('input');
            }
        },

        keydownEvent: function (e)
        {
            var k = e.which;

            // backspace, delete, and escape get special treatment
            if (k == 8 || k == 46 || (this.isIphone && k == 127)) {
                var pos = this.caret(),
                    begin = pos.begin,
                    end = pos.end;

                if (end - begin === 0) {
                    begin = k != 46 ? this.seekPrev(begin) : (end = this.seekNext(begin - 1));
                    end = k == 46 ? this.seekNext(end) : end;
                }

                this.clearBuffer(begin, end);
                this.shiftL(begin, end - 1);

                return false;

            // escape
            } else if (k == 27) {
                this.$phoneField.val(this.focusText);
                this.caret(0, this.checkVal());

                return false;
            }
        },

        keypressEvent: function (e)
        {
            var len = this.mask.length;

            var k = e.which,
                pos = this.caret();

            // Ignore
            if (e.ctrlKey || e.altKey || e.metaKey || k < 32) {
                return true;

            } else if (k) {
                if (pos.end - pos.begin !== 0) {
                    this.clearBuffer(pos.begin, pos.end);
                    this.shiftL(pos.begin, pos.end - 1);
                }

                var p = this.seekNext(pos.begin - 1);

                if (p < len) {
                    var c = String.fromCharCode(k);

                    if (this.tests[p].test(c)) {
                        this.shiftR(p);
                        this.buffer[p] = c;
                        this.writeBuffer();
                        var next = this.seekNext(p);
                        this.caret(next);
                    }
                }

                return false;
            }
        },

        pasteEvent: function ()
        {
            var that = this

            setTimeout(function () {
                that.caret(that.checkVal(true))
            }, 0);
        },

        clearBuffer: function (start, end)
        {
            var len = this.mask.length;

            for (var i = start; i < end && i < len; i++) {
                if (this.tests[i]) {
                    this.buffer[i] = this.options.placeholder
                }
            }
        },

        writeBuffer: function ()
        {
            return this.$phoneField.val(this.buffer.join('')).val()
        },

        seekNext: function (pos)
        {
            var len = this.mask.length;

            while (++pos <= len && !this.tests[pos]);

            return pos;
        },

        seekPrev: function (pos)
        {
            while (--pos >= 0 && !this.tests[pos]);

            return pos;
        },

        shiftL: function (begin, end)
        {
            var len = this.mask.length;

            if (begin < 0) return;

            for (var i = begin, j = this.seekNext(end); i < len; i++) {
                if (this.tests[i]) {
                    if (j < len && this.tests[i].test(this.buffer[j])) {
                        this.buffer[i] = this.buffer[j];
                        this.buffer[j] = this.options.placeholder;

                    } else {
                        break;
                    }

                    j = this.seekNext(j);
                }
            }

            this.writeBuffer();
            this.caret(Math.max(this.firstNonMaskPos, begin));
        },

        shiftR: function (pos)
        {
            var len = this.mask.length;

            for (var i = pos, c = this.options.placeholder; i < len; i++) {
                if (this.tests[i]) {
                    var j = this.seekNext(i);
                    var t = this.buffer[i];

                    this.buffer[i] = c;

                    if (j < len && this.tests[j].test(t)) {
                        c = t;

                    } else {
                        break
                    }
                }
            }
        }
    };

    /**
     * Initialize form phone plugin
     *
     * @param {jQuery} $element
     * @param {Object} options
     */
    IPub.Forms.Phone.initialize = function ($element, options) {
        $element.each(function () {
            var $this = $(this);

            if (!$this.data('ipub-forms-phone')) {
                $this.data('ipub-forms-phone', (new IPub.Forms.Phone($this, options).init()));
            }
        });
    };

    /**
     * Autoloading for form phone plugin
     *
     * @returns {jQuery}
     */
    IPub.Forms.Phone.load = function () {
        return $('[data-ipub-forms-phone]').ipubFormsPhone();
    };

    /**
     * IPub Forms phone plugin definition
     */

    var old = $.fn.ipubFormsPhone;

    $.fn.ipubFormsPhone = function (options) {
        return this.each(function () {
            var $this = $(this);

            if (!$this.data('ipub-forms-phone')) {
                $this.data('ipub-forms-phone', (new IPub.Forms.Phone($this, options).init()));
            }
        });
    };

    /**
     * IPub Forms phone plugin no conflict
     */

    $.fn.ipubFormsPhone.noConflict = function () {
        $.fn.ipubFormsPhone = old;

        return this;
    };

    /**
     * IPub Forms phone plugin default settings
     */

    $.fn.ipubFormsPhone.defaults = {
        mask        : '',
        placeholder : '_',
        definitions : {
            '9' : '[0-9]',
            'a' : '[A-Za-z]',
            'w' : '[A-Za-z0-9]',
            '*' : '.'
        }
    };

    /**
     * Complete plugin
     */

    // Autoload plugin
    IPub.Forms.Phone.load();

    // Autoload for ajax calls
    $(document).ajaxSuccess(function() {
        // Autoload plugin
        IPub.Forms.Phone.load();
    });

    // Assign plugin data to DOM
    window.IPub = IPub;

    return IPub;

})(jQuery, window, document, location, navigator);

/**
 * IPub.forms.phone custom validator
 *
 * @param {obj} elem
 * @param {Array} arg
 * @param {String} value
 */
Nette.validators.IPubFormPhoneFormsPhoneValidator_validatePhone = function (elem, arg, value) {
    if (value.trim() == '') return;

    // Validator params
    var params = new Array;

    // Try to find country slect element
    var countryFieldName = elem.name.substring(0, elem.name.lastIndexOf('[')) + '\[country\]';
    var countryField;

    // If country select element exists...
    if (countryField = document.getElementsByName(countryFieldName)[0]) {
        // ...get value for country check
        params.push(countryField.value);

        // No country selected, set to automatic detection
    } else {
        params.push('AUTO');
    }

    // Try to validate field
    return new IPub.Phone.Validator(elem, params).validate();
};
