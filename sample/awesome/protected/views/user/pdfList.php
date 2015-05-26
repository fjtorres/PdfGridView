<?php 
	$this->widget('ext.PdfGridView', array(
		'dataProvider'=> $dataProvider,
		'outputName'=>'User list',
		'styleFile'=>'css/export-pdf.css',
		'columns'=>array(
		array(
			'name'=>'username', 
			'htmlOptions'=>array('style'=>'width:100px;text-align:center;')
		),
		array(
			'name'=>'password',
			'cssClassExpression'=>'isset($data->password)?"password":"default"',
		),
	),
	));
?>