
<div class="row" style="margin-top: 5%;">
    <div class="col-md-8 col-md-offset-2">
        
        
<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<?php foreach($js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
        
<?php echo $output; ?>
        
    </div>
</div>