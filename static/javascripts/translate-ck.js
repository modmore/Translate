$(function(){Backbone.sync=function(e,t,n){var r=t.actions,i={type:"POST",dataType:"json",data:n.attrs||t.attributes||{}};i.url=""+TranslateConfig.baseUrl+r[e]+"?namespace="+TranslateConfig.namespace+"&topic="+TranslateConfig.topic+"&language="+TranslateConfig.language;var s=n.xhr=Backbone.ajax(_.extend(i,n));t.trigger("request",t,s,n);return s};var e=Backbone.Model.extend({defaults:{id:0,languageset:"",key:"",original:"",translation:"",flagged:!1,skipped:!1,translated:!1},actions:{read:"gettranslations",update:"update",patch:"update"}}),t=Backbone.Collection.extend({model:e,comparator:"id",actions:{read:"gettranslations",update:"update",patch:"update"},flagged:function(){return this.where({flagged:!0})},skipped:function(){return this.where({skipped:!0})},translated:function(){return this.where({translated:!0})},notTranslated:function(){return this.where({translated:!1})},notTranslatedOrSkipped:function(){return this.where({translated:!1,skipped:!1})}}),n=new t,r=Backbone.View.extend({tagName:"div",template:_.template($("#entry-template").html()),events:{"click .skip":"skipEntry","click .save":"saveEntry","click .review":"saveAndReviewEntry","keypress .translation":"updateOnEnter"},initialize:function(){this.listenTo(this.model,"change",this.render);this.listenTo(this.model,"destroy",this.remove)},render:function(){this.$el.html(this.template(this.model.toJSON()));this.input=this.$(".translation");return this},save:function(e){var t=this.input.val();t?this.model.save({translation:t,flagged:e,translated:!0,skipped:!1}):this.skipEntry()},saveEntry:function(){this.save(!1)},saveAndReviewEntry:function(){this.save(!0)},skipEntry:function(){this.model.save({skipped:!0,translated:!1})},updateOnEnter:function(e){if(e.keyCode==13)if(this.input.val().length){console.log("saveEntry");this.saveEntry()}else{console.log("skipEntry");this.skipEntry()}}}),i=Backbone.View.extend({el:$("#container"),completedTpl:_.template($("#completed-template").html()),mode:"translate",events:{},initialize:function(){this.listenTo(n,"change",this.findNext);this.listenTo(n,"reset",this.findNext);this.listenTo(n,"all",this.render);this.main=$("#main");this.stats={translated:this.$(".translated"),total:this.$(".total"),skipped:this.$(".skipped"),credits:this.$(".credits"),percentage:this.$(".percentage")};n.fetch({reset:!0})},render:function(){var e=n.translated().length,t=n.length,r=n.skipped().length,i=523,s=(e/t*100).toFixed(0)+"%";this.stats.translated.text(e);this.stats.total.text(t);this.stats.skipped.text(r);this.stats.credits.text(i);this.stats.percentage.css({width:s})},showEntry:function(e){var t=new r({model:e});this.main.html(t.render().el)},findNext:function(){var e=[];switch(this.mode){case"translate":e=n.notTranslatedOrSkipped();break;case"skipped":e=n.skipped();break;case"flagged":e=n.flagged()}if(e.length){this.showEntry(e[0]);this.main.find("input,textarea").focus()}else this.completedTask()},completedTask:function(){var e=this,t={translate:"new or missing entries to translate",skipped:"skipped entries to translate",flagged:"flagged entries to review"};this.main.fadeOut(function(){var r=n.notTranslatedOrSkipped().length,i=n.skipped().length,s=n.flagged().length;e.main.html(e.completedTpl({what:t[e.mode],translateable:r,skipped:i,flagged:s}));r<1&&e.main.find(".modeTranslate").hide();i<1&&e.main.find(".modeSkipped").hide();s<1&&e.main.find(".modeFlagged").hide();e.main.find(".modeSkipped").on("click",_.bind(e.setModeSkipped,e));e.main.fadeIn()})},setModeTranslate:function(){console.log("setting mode to translate");this.mode="translate";this.findNext()},setModeSkipped:function(){console.log("setting mode to skipped");this.mode="skipped";this.findNext()},setModeFlagged:function(){console.log("setting mode to flagged");this.mode="flagged";this.findNext()}}),s=new i});