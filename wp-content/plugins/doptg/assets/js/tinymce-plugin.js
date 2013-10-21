/*
* Title                   : Thumbnail Gallery (WordPress Plugin)
* Version                 : 2.3
* File                    : tinymce-plugin.php
* File Version            : 1.0
* Created / Last Modified : 07 January 2012
* Author                  : Dot on Paper
* Copyright               : © 2012 Dot on Paper
* Website                 : http://www.dotonpaper.net
* Description             : TinyMCE Editor Plugin.
*/

(function(){
    var title, i,
    galleriesData,
    galleries = new Array();

    tinymce.create('tinymce.plugins.DOPTG', {
        init:function(ed, url){
            title = DOPTG_tinyMCE_data.split(';;;;;')[0];
            galleriesData = DOPTG_tinyMCE_data.split(';;;;;')[1];
            galleries = galleriesData.split(';;;');
        },

        createControl:function(n, cm){// Init Combo Box.
            switch (n){
                case 'DOPTG':
                    var mlb = cm.createListBox('DOPTG', {
                         title: title,
                         onselect: function(value){
                             tinyMCE.activeEditor.selection.setContent(value);
                         }
                    });

                    for (i=0; i<galleries.length; i++){
                        if (galleries[i] != ''){
                            mlb.add('ID '+galleries[i].split(';;')[0]+': '+galleries[i].split(';;')[1], '[doptg id="'+galleries[i].split(';;')[0]+'"]');
                        }
                    }
                    
                    return mlb;
            }

            return null;
        },

        getInfo:function(){
            return {longname  : 'Thumbnail Gallery',
                    author    : 'Marius-Cristian Donea',
                    authorurl : 'http://www.mariuscristiandonea.com',
                    infourl   : 'http://www.mariuscristiandonea.com',
                    version   : '1.0'};
        }
    });

    tinymce.PluginManager.add('DOPTG', tinymce.plugins.DOPTG);
})();