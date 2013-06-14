
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="3rdparty/fileupload/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="3rdparty/fileupload/css/jquery.fileupload-ui-noscript.css"></noscript>
<!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

<div class="container-fluid">
<?php
if($widget->properties['showBox']):
 $this->beginWidget('UIPanel', array('label'=>$widget->label, 'icond'=>'ico-pencil'));
endif;?>
<?php echo $widget->tabBar?>
    <!-- The file upload form used as target for the file upload widget -->
    <form id="<?php echo $widget->GetId()?>" action="<?php echo $widget->postUrl?>" method="POST" enctype="multipart/form-data">
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
                <button type="button" class="btn delete">
                    删除所选
                </button>
                <input type="checkbox" class="toggle" id="checkallfile">全选	
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
        <table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
    </form>
    

<?php 
if($widget->properties['showBox']):
$this->endWidget();
endif;?>
</div>
<!-- modal-gallery is the modal dialog used for the image gallery -->
<div id="modal-gallery" class="modal modal-gallery hide fade" data-filter=":odd" tabindex="-1">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3 class="modal-title"></h3>
    </div>
    <div class="modal-body"><div class="modal-image"></div></div>
    <div class="modal-footer">
        <a class="btn modal-download" target="_blank">
            <i class="icon-download"></i>
            <span>Download</span>
        </a>
        <a class="btn btn-success modal-play modal-slideshow" data-slideshow="5000">
            <i class="icon-play icon-white"></i>
            <span>Slideshow</span>
        </a>
        <a class="btn btn-info modal-prev">
            <i class="icon-arrow-left icon-white"></i>
            <span>Previous</span>
        </a>
        <a class="btn btn-primary modal-next">
            <span>Next</span>
            <i class="icon-arrow-right icon-white"></i>
        </a>
    </div>
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
            <input type="checkbox" class="checkbox" name="delete" value="1">
<?php endif?>
        </td>
    </tr>
{% } %}
</script>
<script src="js/jquery.ui.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="3rdparty/fileupload/js/tmpl.js"></script>
<!-- Bootstrap JS and Bootstrap Image Gallery are not required, but included for the demo -->
<script src="js/bootstrap.js"></script>
<!-- The basic File Upload plugin -->
<script src="3rdparty/fileupload/js/jquery.fileupload.js"></script>
<!-- The File Upload file processing plugin -->
<script src="3rdparty/fileupload/js/jquery.fileupload-fp.js"></script>
<!-- The File Upload user interface plugin -->
<script src="3rdparty/fileupload/js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
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
