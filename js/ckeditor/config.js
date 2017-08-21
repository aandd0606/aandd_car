/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function(config) {
    //界面語言，預設為 'en'，我們改成繁體中文
    config.language='zh';
    // 編輯器樣式，有三種：'kama'（預設）、'office2003'、'v2'我們改成office2003
    config.skin='office2003';
    // 背景顏色
    config.uiColor='#000000';
    // 設置寬高單位為畫素
    config.width =900;
    config.height  =300;
     //工具欄是否可以被收縮
    config.toolbarCanCollapse = true;
    //工具欄的位置
    config.toolbarLocation = 'top';//可選：bottom
    //工具欄預設是否展開
    config.toolbarStartupExpanded = true;
    // 取消 「拖拽以改變尺寸」功能 plugins/resize/plugin.js
    config.resize_enabled = false;
     //改變大小的最大高度
    config.resize_maxHeight = 3000;
    //改變大小的最大寬度
    config.resize_maxWidth = 3000;
    //改變大小的最小高度
    config.resize_minHeight = 250;
    //改變大小的最小寬度
    config.resize_minWidth = 750;
    //字體預設大小 plugins/font/plugin.js
    config.fontSize_defaultLabel = '12px';
    /*
     [
//加粗     斜體，     下劃線      穿過線      下標字        上標字
['Bold','Italic','Underline','Strike','Subscript','Superscript'],
//數字列表          實體列表            減小縮進    增大縮進
['NumberedList','BulletedList','-','Outdent','Indent'],
//左對齊             居中對齊          右對齊          兩端對齊
['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
//超鏈接 取消超鏈接 錨點
['Link','Unlink','Anchor'],
//圖片    flash    表格       水平線            表情       特殊字符        分頁符
['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
'/',
//樣式       格式      字體    字體大小
['Styles','Format','Font','FontSize'],
//文本顏色     背景顏色
['TextColor','BGColor'],
//全屏           顯示區塊
['Maximize', 'ShowBlocks','-']
             ]
    */
    
	config.toolbar = 'MyToolbar';
    // 工具欄（基礎'Basic'、全能'Full'、自定義）plugins/toolbar/plugin.js
    // config.toolbar = 'Basic';
    //config.toolbar = 'Full';
    config.toolbar_Full = [
       ['Source','-','Save','NewPage','Preview','-','Templates'],
       ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
       ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
       ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
       '/',
       ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
        ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['Link','Unlink','Anchor'],
       ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
       '/',
        ['Styles','Format','Font','FontSize'],
        ['TextColor','BGColor']
    ];
    
    config.toolbar = 'Full';

   
   config.filebrowserBrowseUrl = 'js/ckeditor/kcfinder/browse.php?type=files';
   config.filebrowserImageBrowseUrl = 'js/ckeditor/kcfinder/browse.php?type=images';
   config.filebrowserFlashBrowseUrl = 'js/ckeditor/kcfinder/browse.php?type=flash';
   config.filebrowserUploadUrl = 'js/ckeditor/kcfinder/upload.php?type=files';
   config.filebrowserImageUploadUrl = 'js/ckeditor/kcfinder/upload.php?type=images';
   config.filebrowserFlashUploadUrl = 'js/ckeditor/kcfinder/upload.php?type=flash';
};
