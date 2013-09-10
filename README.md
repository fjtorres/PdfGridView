PdfGridView
===========

Yii extension for export grid view to PDF with MPDF.

- [Sample](#sample-code)

## Sample code:

```php
<?php 
	$this->widget('ext.PdfGridView', array(
		'dataProvider'=> $dataProvider,
		'outputName'=>$exportName,
		'styleFile'=>'css/export-pdf.css',
		'columns'=>array(
		array(
			'name'=>'column1', 
			'htmlOptions'=>array('style'=>'width:100px;text-align:center;')
		),
		'column1',

	),
	));
?>
´´´