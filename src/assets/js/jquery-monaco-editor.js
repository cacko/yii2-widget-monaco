(function ($) {
    class MonacoEditor {

        _target = $();
        _themeSelector = $();
        _editor = null;
        _model = null;
        _input = $();
        _resizeInterval = null;
        _themeInterval = null;
        _initialized = false;

        constructor(options) {
            this._target = options._target;
            this.options = options;
            this._themeSelector = this._target.find(this.options.themeSelector);
            this._input = this._target.find(`${this.options.inputSelector}`);
            if (this.options.useFullHeight) {
                const height = $(this.options.useFullHeight).height();
                if (height) {
                    this.options.height = height;
                }
            }
            this.initEditor().then(editor => {
                this._editor = editor;
                this._model = this._editor.getModel();
                this.registerListeners();
                this._target.trigger('ready');
            });
        }

        initEditor() {
            this._target.css({
                width: this.options.width || '100%',
                height: this.options.height || '100%',
            });
            return new Promise(resolve => {
                require(["vs/editor/editor.main"], () =>
                    resolve(monaco.editor.create(
                        document.getElementById(this.options.editorId),
                        $.extend(this.options.editorConfig, {
                            value: this._input.val() || ''
                        }
                        )
                    ))
                );
            });
        }

        loadNew(input, lang) {
            return new Promise(resolve => {
                this._input = $(input);
                const model = this._editor.getModel();
                if (model) {
                    model.dispose();
                    // this._editor.dispose();
                }
                this._model = monaco.editor.createModel(this._input.val(), lang);
                this._editor.setModel(this._model);
                this._model.onDidChangeContent($.proxy(this.onEdit, this));
                resolve(true);
            });
        }

        registerListeners() {
            this._themeSelector.on('click', $.proxy(this.onThemeSelector, this));
            if (this.options.resizable) {
                this._target.css({
                    resize: 'vertical',
                    minHeight: this.options.minHeight
                });
                new ResizeObserver($.proxy(this.onResize, this)).observe(this._target.get(0));
            }
            this._model.onDidChangeContent($.proxy(this.onEdit, this));
        }

        onEdit() {
            const val = this._model.getValue();
            this._target.trigger('editor.edit', [val]);
            this._input.val(val);
        }

        onThemeSelector() {
            const theme = this._themeSelector.hasClass('on-dark') ? this.options.themes.light : this.options.themes.dark;
            this._themeSelector.toggleClass('on-dark');
            monaco.editor.setTheme(theme);
            this.saveTheme(theme);

        }

        saveTheme(theme) {
            this._themeInterval && clearInterval(this._themeInterval);
            this._themeInterval = setInterval(() =>
                this.saveSettings({theme}) && clearInterval(this._themeInterval), 3000);
        }

        saveHeight(height) {
            this.saveSettings({height});
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
            })
        }

        onResize() {
            this._editor.layout({ height: this._target.height(), width: this._target.width() });
            if (!this._initialized) {
                this._initialized = true;
                return true;
            }
            this._resizeInterval && clearInterval(this._resizeInterval);
            this._resizeInterval = setInterval(() => {
                clearInterval(this._resizeInterval);
                if (!this.options.useFullHeight) {
                    this.saveHeight(this._target.height());
                }
            }, 3000);
        }
    }

    $.fn.monacoEditor = function (option) {
        const args = arguments;

        return this.each(function () {
            let data = $(this).data('MonacoEditor');
            const options = typeof option === 'object' ? option : {};

            if (data === undefined) {
                const defaultOptions = $.extend(true, {}, $.fn.monacoEditor.defaults);
                options._target = $(this);

                $(this).data('MonacoEditor', (data = new MonacoEditor(
                    $.extend(defaultOptions, options)
                )));
            }

            if (typeof option === 'string') { //call method
                data[option].apply(data, Array.prototype.slice.call(args, 1));
            }
        });
    };

    $.fn.monacoEditor.defaults = {
        themeSelector: ".theme-selector",
        editorConfig: {},
        editorId: null,
        inputSelector: null,
        userSettingsUrl: '#',
        resizable: true,
        minHeight: '5rem',
        height: null,
        width: null,
        useFullHeight: '',
        themes: {},
    };
})
    (jQuery);