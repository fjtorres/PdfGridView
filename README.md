PdfGridView
===========

Yii extension for export grid view to PDF with MPDF. This version support MPDF 6.0.

- [Change log](#change-log)
- [Sample](#sample-code)
- [Resources](#resources)

# Change log

- 0.2: Added cssClassExpression attribute to column and license (apache license v2). Change from MPDF 5.6 to MPDF 6.0.

# Sample code:

```php
<?php 
	$this->widget('ext.PdfGridView', array(
		'dataProvider'=> $dataProvider,
		'outputName'=>$exportName,
		'styleFile'=>'css/export-pdf.css',
		'rowCssClassExpression'=>'Expression for the row css class'
		'columns'=>array(
		array(
			'name'=>'column1', 
			'htmlOptions'=>array('style'=>'width:100px;text-align:center;'),
			'cssClassExpression'=>'Expression for the column css class'
		),
		'column1',

	),
	));
?>
```

# Resources

MPDF: [http://www.mpdf1.com/](http://www.mpdf1.com/)