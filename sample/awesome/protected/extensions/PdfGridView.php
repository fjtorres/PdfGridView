<?php
/*
	Copyright [2015] [FJTORRES]

	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

	    http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.
*/
Yii::import('zii.widgets.grid.CGridView');

/**
 * Yii extension for export gridview to PDF with MPDF library.
 * @author FJTORRES
 * @version 0.3
 */
class PdfGridView extends CGridView {

	const PDF_EXT = ".pdf";
	const EXPORT = "D";

	private $pdfObject;
	private $outputHtml;
	private $style;

	/**
	 * Output file name.
	 */
	public $outputName = "export";

	/**
	 * CSS file to import in export process.
	 */
	public $styleFile;

	/**
	 * Margin left to PDF file.
	 */
	public $marginLeft = 10;

	/**
	 * Margin right to PDF file.
	 */
	public $marginRight = 10;

	/**
	 * Margin top to PDF file.
	 */
	public $marginTop = 10;

	/**
	 * Margin bottom to PDF file.
	 */
	public $marginBottom = 10;

	/**
	 * Default font family to PDF file.
	 */
	public $defaultFontFamily = "verdana";

	/**
	 * Default font size to PDF file.
	 */
	public $defaultFontSize = 0;

	/**
	* Text for empty PDF file.
	*/
	public $emptyText = "Data not found";

	/**
	* Css class for empty text.
	*/
	public $emptyCssClass;

	/**
	* Text for header of PDF file.
	*/
	public $headerText = "Data not found";

	/**
	* Css class for header.
	*/
	public $headerCssClass;

	public function init() {
		parent::init();

		Yii::createComponent('application.extensions.mpdf60.mpdf');

		$this->pdfObject = new mPDF('', 'A4', $this->defaultFontSize, $this->defaultFontFamily, $this->marginLeft,
				$this->marginRight, $this->marginTop, $this->marginBottom, 0, 0, 'landscape');

		$this->style = "<style type='text/css'>";
		if (!isset($this->styleFile)) {
			// Default styles
			$this->style .= "table {width: 100%;border-spacing:0px;border: 1px solid #000;}";
			$this->style .= "table td, table th {border: 1px solid #000;margin: 2px;height: 30px;}";
		} else {
			$this->style .= file_get_contents($this->styleFile);
		}
		$this->style .= "</style>";
	}

	public function run() {
		$this->writePdf();
		$this->pdfObject->AddPage("A4-L");
		$this->pdfObject->WriteHTML($this->outputHtml);
		$this->pdfObject->Output($this->outputName . self::PDF_EXT, self::EXPORT);
		Yii::app()->end();
	}

	/**
	 * Function to write html data from grid view in PDF.
	 */
	private function writePdf() {
		$this->write($this->style);
		if ($this->hasData()) {

			$this->writeReportHeader();

			$this->write(CHtml::openTag("table"));
			$this->writeHeader();
			$this->writeBody();
			$this->write(CHtml::closeTag('table'));
		} else {
			$emptyHtmlOptions = array();
			
			if ($this->emptyCssClass !== null) {
				$emptyHtmlOptions['class'] = $this->emptyCssClass;
			}

			$this->write(CHtml::openTag("div", $emptyHtmlOptions));
			$this->write($this->emptyText);
			$this->write(CHtml::closeTag("div"));
		}
	}

	/**
	 * Function to write grid header, if needed.
	 */
	private function writeHeader() {
		if (!$this->hideHeader) {
			$this->write(CHtml::openTag("thead"));
			$this->write(CHtml::openTag("tr"));

			foreach ($this->columns as $column) {

				if ($column instanceof CButtonColumn) {
					$head = $column->header;
				} else if ($column->header === null && $column->name !== null) {
					if ($column->grid->dataProvider instanceof CActiveDataProvider) {
						$head = $column->grid->dataProvider->model->getAttributeLabel($column->name);
					} else {
						$head = $column->name;
					}
				} else {
					$head = trim($column->header) !== '' ? $column->header : $column->grid->blankDisplay;
				}

				$this->write(CHtml::openTag("th"));
				$this->write($head);
				$this->write(CHtml::closeTag("th"));
			}

			$this->write(CHtml::closeTag("tr"));
			$this->write(CHtml::closeTag("thead"));
		}
	}

	/**
	 * Function to write grid body
	 * @return number of data elements.
	 */
	private function writeBody() {
		$this->dataProvider->pagination = false;

		$data = $this->dataProvider->getData(true);
		$n = count($data);

		if ($n > 0) {
			$this->write(CHtml::openTag("tbody"));

			for ($row = 0; $row < $n; ++$row) {
				$this->writeRow($row);
			}

			$this->write(CHtml::closeTag("tbody"));
		}
		return $n;
	}

	/**
	 * Function to write item row.
	 * @param $row Number Number of the row to write. 
	 */
	private function writeRow($row) {
		$data = $this->dataProvider->getData();

		$this->openRow();

		foreach ($this->columns as $n => $column) {

			if ($column instanceof CLinkColumn) {
				if ($column->labelExpression !== null) {
					$value = $column
							->evaluateExpression($column->labelExpression,
									array(
										'data' => $data[$row], 'row' => $row
									));
				} else {
					$value = $column->label;
				}
			} else if ($column instanceof CButtonColumn) {
				$value = "";
			} else if ($column->value !== null) {
				$value = $this->evaluateExpression($column->value, array(
							'data' => $data[$row]
						));
			} else if ($column->name !== null) {
				$value = CHtml::value($data[$row], $column->name);
				$value = $value === null ? "" : $column->grid->getFormatter()->format($value, 'raw');
			}

			$options = $column->htmlOptions;

			// CSS class for column by expression	
			if($column->cssClassExpression !== null) {
				
				$class=$this->evaluateExpression($column->cssClassExpression,array('row'=>$row,'data'=>$data));
				
				if(!empty($class)) {
					if(isset($options['class']))
						$options['class'] .= ' ' . $class;
					else
						$options['class'] = $class;
				}
			}

			$this->write(CHtml::openTag('td', $options));
			$this->write($value);
			$this->write(CHtml::closeTag('td'));
		}

		$this->closeRow();
	}

	/**
	 * Function to write open row tag.
	 */
	private function openRow() {
		$htmlOptions = array();

		if ($this->rowHtmlOptionsExpression !== null) {
			$data = $this->dataProvider->data[$row];
			$options = $this
					->evaluateExpression($this->rowHtmlOptionsExpression,
							array(
								'row' => $row, 'data' => $data
							));
			if (is_array($options)) {
				$htmlOptions = $options;
			}
		}

		if ($this->rowCssClassExpression !== null) {
			$data = $this->dataProvider->data[$row];
			$class = $this
					->evaluateExpression($this->rowCssClassExpression,
							array(
								'row' => $row, 'data' => $data
							));
		} elseif (is_array($this->rowCssClass) && ($n = count($this->rowCssClass)) > 0)
			$class = $this->rowCssClass[$row % $n];

		if (!empty($class)) {
			if (isset($htmlOptions['class'])) {
				$htmlOptions['class'] .= ' ' . $class;
			} else {
				$htmlOptions['class'] = $class;
			}
		}

		$this->write(CHtml::openTag('tr', $htmlOptions));
		$this->write("\n");
	}

	/**
	 * Function to write close row tag.
	 */
	private function closeRow() {
		$this->write(CHtml::closeTag('tr'));
		$this->write("\n");
	}

	/**
	 * Function to write to the output.
	 * @param $str String Value to write.
	 */
	private function write($str = "") {
		if (!isset($this->outputHtml)) {
			$this->outputHtml = $str;
		} else {
			$this->outputHtml .= $str;
		}
	}

	/**
	* Check data of report.
	* @return true if report has data, false otherwise.
	*/
	private function hasData () {
		$this->dataProvider->pagination = false;

		$data = $this->dataProvider->getData(true);
		$n = count($data);

		return $n > 0;
	}

	private function writeReportHeader () {
		if ($this->headerText !== null) {

			$headerHtmlOptions = array();

			if ($this->headerCssClass !== null) {
				$headerHtmlOptions['class'] = $this->headerCssClass;
			}
			$this->write(CHtml::openTag("div", $headerHtmlOptions));
			$this->write($this->headerText);
			$this->write(CHtml::closeTag("div"));
		}
	}

}
