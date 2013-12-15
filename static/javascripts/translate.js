$(function () {
    Backbone.sync = function(method, model, options) {
        var actions = model.actions;

        // Default JSON-request options.
        var params = {
            type: 'POST',
            dataType: 'json',
            data: options.attrs || model.attributes || {}
        };

        params.url = '' + TranslateConfig.baseUrl + actions[method] + '?namespace=' + TranslateConfig.namespace + '&topic=' + TranslateConfig.topic + '&language=' + TranslateConfig.language;

        // Make the request, allowing the user to override any Ajax options.
        var xhr = options.xhr = Backbone.ajax(_.extend(params, options));
        model.trigger('request', model, xhr, options);
        return xhr;
    };

    var Entry = Backbone.Model.extend({
        defaults: {
            id: 0,
            languageset: '',
            key: '',
            original: '',
            translation: '',
            flagged: false,
            skipped: false,
            translated: false
        },

        actions: {
            read: 'gettranslations',
            update: 'update',
            patch: 'update'
        }
    });
    var EntryList = Backbone.Collection.extend({
        model: Entry,
        comparator: 'id',

        actions: {
            read: 'gettranslations',
            update: 'update',
            patch: 'update'
        },

        flagged: function() {
            return this.where({flagged: true});
        },

        skipped: function() {
            return this.where({skipped: true});
        },

        translated: function() {
            return this.where({translated: true});
        },

        notTranslated: function() {
            return this.where({translated: false});
        },

        notTranslatedOrSkipped: function() {
            return this.where({translated: false, skipped: false});
        }
    });

    // Create our global collection of **Entries**.
    var Entries = new EntryList;


    // EntryView
    var EntryView = Backbone.View.extend({

        //... is a list tag.
        tagName: "div",

        // Cache the template function for a single item.
        template: _.template($('#entry-template').html()),

        // The DOM events specific to an item.
        events: {
            "click .skip": "skipEntry",
            "click .save": "saveEntry",
            "click .review": "saveAndReviewEntry",
            "keypress .translation": "updateOnEnter"
        },

        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },

        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
            this.input = this.$('.translation');
            return this;
        },

        save: function (flagged) {
            var value = this.input.val();
            if (!value) {
                this.skipEntry();
            } else {
                this.model.save({
                    translation: value,
                    flagged: flagged,
                    translated: true,
                    skipped: false
                });
            }
        },

        saveEntry: function() {
            this.save(false);
        },

        saveAndReviewEntry: function () {
            this.save(true);
        },

        skipEntry: function () {
            this.model.save({
                skipped: true,
                translated: false
            })
        },

        // If you hit `enter`, we're through editing the item.
        updateOnEnter: function (e) {
            if (e.keyCode == 13) {
                if (this.input.val().length) {
                    console.log('saveEntry');
                    this.saveEntry();
                }
                else {
                    console.log('skipEntry');
                    this.skipEntry();
                }
            }
        }

    });

    // The Application
    // ---------------

    // Our overall **AppView** is the top-level piece of UI.
    var AppView = Backbone.View.extend({

        // Instead of generating a new element, bind to the existing skeleton of
        // the App already present in the HTML.
        el: $("#container"),

        completedTpl: _.template($('#completed-template').html()),

        mode: 'translate',

        // Delegated events for creating new items, and clearing completed ones.
        events: {
            //"click .modeTranslate": "this.setModeTranslate",
            //"click .modeSkipped": "this.setModeSkipped",
            //"click .modeFlagged": "this.setModeFlagged"
        },

        // At initialization we bind to the relevant events on the `Entries`
        // collection, when items are added or changed. Kick things off by
        // loading any preexisting todos that might be saved in *localStorage*.
        initialize: function () {
            this.listenTo(Entries, 'change', this.findNext);
            this.listenTo(Entries, 'reset', this.findNext);
            this.listenTo(Entries, 'all', this.render);

            this.main = $('#main');

            this.stats = {
                translated: this.$('.translated'),
                total: this.$('.total'),
                skipped: this.$('.skipped'),
                credits: this.$('.credits'),
                percentage: this.$('.percentage')
            };

            Entries.fetch({
                reset: true
            });
        },

        // Re-rendering the App just means refreshing the statistics -- the rest
        // of the app doesn't change.
        render: function () {
            var translated = Entries.translated().length,
                total = Entries.length,
                skipped = Entries.skipped().length,
                credits = 523,
                percentage = ((translated / total) * 100).toFixed(0) + '%';

            this.stats.translated.text(translated);
            this.stats.total.text(total);
            this.stats.skipped.text(skipped);
            this.stats.credits.text(credits);
            this.stats.percentage.css({width: percentage});
        },

        showEntry: function (entry) {
            var view = new EntryView({model: entry});
            this.main.html(view.render().el);
        },

        findNext: function () {
            var options = [];
            switch (this.mode) {
                case 'translate':
                    options = Entries.notTranslatedOrSkipped();
                    break;

                case 'skipped':
                    options = Entries.skipped();
                    break;

                case 'flagged':
                    options = Entries.flagged();
                    break;
            }


            if (options.length) {
                this.showEntry(options[0]);
                this.main.find('input,textarea').focus();
            }
            else {
                this.completedTask();
            }
        },

        completedTask: function() {
            var that = this,
                whatOptions = {
                    translate: 'new or missing entries to translate',
                    skipped: 'skipped entries to translate',
                    flagged: 'flagged entries to review'
                };

            this.main.fadeOut(function() {
                var translateable = Entries.notTranslatedOrSkipped().length,
                    skipped = Entries.skipped().length,
                    flagged = Entries.flagged().length;

                that.main.html(that.completedTpl({
                    what: whatOptions[that.mode],
                    translateable: translateable,
                    skipped: skipped,
                    flagged: flagged
                }));

                if (translateable < 1) that.main.find('.modeTranslate').hide();
                if (skipped < 1) that.main.find('.modeSkipped').hide();
                if (flagged < 1) that.main.find('.modeFlagged').hide();

                that.main.find('.modeSkipped').on('click', _.bind(that.setModeSkipped, that));

                that.main.fadeIn();
            });
        },

        setModeTranslate: function() {
            console.log('setting mode to translate');
            this.mode = 'translate';
            this.findNext();
        },

        setModeSkipped: function() {
            console.log('setting mode to skipped');
            this.mode = 'skipped';
            this.findNext();
        },

        setModeFlagged: function() {
            console.log('setting mode to flagged');
            this.mode = 'flagged';
            this.findNext();
        }
    });

    // Finally, we kick things off by creating the **App**.
    var App = new AppView;
});
