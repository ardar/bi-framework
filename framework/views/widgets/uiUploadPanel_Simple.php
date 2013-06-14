<?php Fw::Html()->Header('cssfile', "3rdparty/fileupload/css/jquery.fileupload-ui.css");?>
<!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

<div class="container-fluid">
<?php
if($widget->properties['showBox']):
 $this->beginWidget('UIPanel', array('label'=>$widget->label, 'icond'=>'ico-pencil'));
endif;?>
<?php echo $widget->tabBar?>
<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        
<?php if(!$widget->properties['readonly']):?>
        <div class="row fileupload-buttonbar">
            <div class="span8">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                   添加附件...
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    全部上传
                </button>
                <button type="reset" class="btn cancel">
                    全部取消
                </button>
            </div>
            <!-- The global progress information -->
            <div class="span4 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="bar" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>
        <hr>
<?php endif;//if readonly?>
        <!-- The table listing the files available for upload/download -->
<table role="presentation" class="table table-striped">
<tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
</table>

    

<?php 
if($widget->properties['showBox']):
$this->endWidget();
endif;?>
</div>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary btn-mini">
                    开始上传
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn-mini">
                取消
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
<?php if(!$widget->properties['readonly']):?>
            <button class="btn-mini btn" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                删除
            </button>
<?php endif?>
        </td>
    </tr>
{% } %}
</script>
<?php Fw::Html()->Header('jsfile', "js/jquery.ui.js");?>
<?php Fw::Html()->Header('jsfile', "3rdparty/fileupload/js/tmpl.js");?>
<?php Fw::Html()->Header('jsfile', "3rdparty/fileupload/js/jquery.fileupload.js");?>
<?php Fw::Html()->Header('jsfile', "3rdparty/fileupload/js/jquery.fileupload-fp.js");?>
<?php Fw::Html()->Header('jsfile', "3rdparty/fileupload/js/jquery.fileupload-ui.js");?>
<script>
$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#<?php echo $widget->GetId()?>').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: '<?php echo $widget->handlerUrl?>'
    });

    // Enable iframe cross-domain access via redirect option:
    $('#<?php echo $widget->GetId()?>').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

	// Load existing files:
	$.ajax({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: $('#<?php echo $widget->GetId()?>').fileupload('option', 'url'),
		dataType: 'json',
		context: $('#<?php echo $widget->GetId()?>')[0]
	}).done(function (result) {
		$(this).fileupload('option', 'done')
			.call(this, null, {result: result});
	});

});
</script>
