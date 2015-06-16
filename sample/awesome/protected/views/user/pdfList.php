<?php 
	$this->widget('ext.PdfGridView', array(
		'dataProvider'=> $dataProvider,
		'outputName'=>'User list',
		'styleFile'=>'css/export-pdf.css',
		'emptyText'=>'Listado de usuarios vacio.',
		'emptyCssClass'=>'emptyReport',
		'headerText'=>'Listado de usuarios',
		'headerCssClass'=>'tituloReport',
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