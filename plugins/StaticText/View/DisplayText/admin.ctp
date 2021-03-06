<!-- Shows a text-editor for formating and editing texts -->
<h2><?php echo  __d('static_text', 'Set Text');?></h2>
<?php
	echo $this->element('admin_menu', array('contentId' => $contentId));
	//embedding the needed scripts
	echo $this->Html->script('ckeditor/ckeditor', false);
	echo $this->Html->script('ckeditor/adapters/jquery', false);
?>
<div class="texteditor">
<?php 
	echo $this->Form->create('null');
	echo $this->Form->textarea('Text');
?>
<p>
<?php
	//options for the radiobuttons
	$pub = __d('static_text',' published ');
	$unpub = __d('static_text',' unpublished ');
	$pub   = "<label>$pub</label>";
	$unpub = "<label>$unpub</label>";
	$options =  array(
		// Not text
		'label' 	=> false,
		'type' 		=> 'radio',
		// no legend
		'legend' 	=> false,
		// Values for the radiobuttons
		'options'	=> array(1 => $pub, 0 =>$unpub )
	);
	echo $this->Form->input('Published', $options);?>
</p>
</div>
<?php 
	$end = __d('static_text','Save');
	echo $this->Form->end($end);
	echo $this->Html->script('/static_text/js/editText', false);
?>