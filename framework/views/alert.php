
<script>
$(function() {
	$('#messageModal').modal('toggle');
	$('#messageModal').on('hidden', function () {
	     <?php echo $script_onclick?>
	   })
});
</script>
<div id="messageModal" class="modal message fade hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" >
                <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                   <h3><?php echo $title?></h3>
                </div>
                <div class="modal-body">
                   <h4><?php echo $message?></h4>
                   <p><?php echo $exMessage?></p>
                </div>
                <div class="modal-footer">
                   <!-- button class="btn" data-dismiss="modal">Close</button> -->
                   <button class="btn btn-primary" data-dismiss="modal">确定</button>
                </div>
</div>