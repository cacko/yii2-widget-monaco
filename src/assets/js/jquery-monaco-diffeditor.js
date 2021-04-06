// jshint undef:true
(function ($) {
    class MonacoDiffEditor {

        _target = $();
        _themeSelector = $();
        _editor = null;
        _left = $();
        _right = $();
        _themeInterval = null;
        _layoutSelector = $();
        _sideBySideInterval = null;
        _initialized = false;
        _navigator = null;
        _renderSideBySide = null;

        constructor(options) {
            this._target = options._target;
            this.options = options;
            this._themeSelector = this._target.find(this.options.themeSelector);
            this._layoutSelector = this._target.find(this.options.layoutSelector);
            this._renderSideBySide = this.options.renderSideBySide;
            this._left = this._target.find(this.options.inputLeftSelector).first();
            this._right = this._target.find(this.options.inputRightSelector).first();
            if (this.options.useFullHeight) {
                const height = $(this.options.useFullHeight).height();
                if (height) {
                    this.options.height = height;
                }
            }
            this.initEditor().then(editor => {
                this._editor = editor;
                this._navigator = monaco.editor.createDiffNavigator(editor, {
                    followsCaret: true, // resets the navigator state when the user selects something in the editor
                    ignoreCharChanges: true, // jump from line to line
                    alwaysRevealFirst: true
                });
                this.registerListeners();
            });
        }

        initEditor() {
            this._target.css({
                height: this.options.height || '25rem',
            });
            return new Promise(resolve => {
                require(["vs/editor/editor.main"], () => {
                    const editor = monaco.editor.createDiffEditor(document.getElementById(this.options.editorId), this.options.editorConfig);
                    editor.setModel({
                        original: monaco.editor.createModel(this._left.val(), this.options.editorConfig.language),
                        modified: monaco.editor.createModel(this._right.val(), this.options.editorConfig.language),
                    });
                    resolve(editor);
                });
            });
        }

        registerListeners() {
            this._themeSelector.on('click', $.proxy(this.onThemeSelector, this));
            this._layoutSelector.on('click', $.proxy(this.toggleLayout, this));
            this._target.on('sideBySide.monacoDiffEditor', $.proxy(this.onSideBySide, this));
            this._target.on('updated.theme.monaco', $.proxy(this.onThemeUpdate, this));
            if (this.options.resizable) {
                this._target.css({
                    resize: 'vertical',
                    minHeight: this.options.minHeight
                });
                new ResizeObserver($.proxy(this.onResize, this)).observe(this._target.get(0));
            }
        }

        updateModified(val) {
            const editor = this._editor.getModifiedEditor();
            const model = editor.getModel();
            model.setValue(val);
        }

        onEdit() {
            this._input.val(this._model.getValue());
        }

        onThemeSelector() {
            const theme = this._themeSelector.hasClass('on-dark') ? this.options.themes.light : this.options.themes.dark;
            this._themeSelector.toggleClass('on-dark');
            this._layoutSelector.toggleClass('on-dark');
            this.monaco.editor.setTheme(theme);
            this.saveTheme(theme);
            $('.cacko-widget-monaco').not(`#${this._target.attr('id')}`).trigger('updated.theme.monaco', [theme]);
        }

        onThemeUpdate(e, theme) {
            this._layoutSelector.toggleClass('on-dark', this.options.themes.dark === theme);
        }

        toggleLayout() {
            this.onSideBySide(null, !this._renderSideBySide);
        }

        onSideBySide(e, renderSideBySide) {
            this._renderSideBySide = renderSideBySide;
            this._editor.updateOptions({ renderSideBySide });
            this.saveSideBySide(renderSideBySide);
        }

        saveTheme(theme) {
            this._themeInterval && clearInterval(this._themeInterval);
            this._themeInterval = setInterval(() =>
                this.saveSettings({ theme }) && clearInterval(this._themeInterval), 3000);
        }

        saveSideBySide(renderSideBySide) {
            this._sideBySideInterval && clearInterval(this._sideBySideInterval);
            this._sideBySideInterval = setInterval(() =>
                this.saveSettings({ renderSideBySide }) && clearInterval(this._sideBySideInterval), 3000);
        }

        saveSettings(payload) {
            if (this.options.userSettingsUrl === '#') {
                return;
            }
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                url: this.options.userSettingsUrl,
                data: JSON.stringify(payload)
            });
        }

        onResize() {
            this._editor.layout({ height: this._target.height(), width: this._target.width() });
            if (!this._initialized) {
                this._initialized = true;
                return true;
            }
        }
    }

    $.fn.monacoDiffEditor = function (option) {
        const args = arguments;

        return this.each(function () {
            let data = $(this).data('MonacoDiffEditor');
            const options = typeof option === 'object' ? option : {};

            if (data === undefined) {
                const defaultOptions = $.extend(true, {}, $.fn.monacoDiffEditor.defaults);
                options._target = $(this);

                $(this).data('MonacoDiffEditor', (data = new MonacoDiffEditor(
                    $.extend(defaultOptions, options)
                )));
            }

            if (typeof option === 'string') { //call method
                data[option].apply(data, Array.prototype.slice.call(args, 1));
            }
        });
    };

    $.fn.monacoDiffEditor.defaults = {
        themeSelector: ".theme-selector",
        editorConfig: {},
        editorId: null,
        inputLeftSelector: '[name="parent"]',
        inputRightSelector: '[name="current"]',
        userSettingsUrl: '#',
        resizable: true,
        minHeight: '5rem',
        height: null,
        width: null,
        controllerId: '',
        useFullHeight: '',
        themes: {},
        layoutSelector: '.layout-selector'
    };
}

)
    (jQuery);