<?php

require_once (FRAMEWORK_PATH . 'base/controller/Base.controller.php');
require_once PLUGIN_PATH . '/phpexcel/PHPExcel.php';

class HighChartsController extends BaseController {

	public function init() {
		
	}

	public function C_Down() {
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties 设置文档的属性
		$objPHPExcel->getProperties()
				->setCreator("Maarten Balliauw")//创建人
				->setLastModifiedBy("Maarten Balliauw")//最后修改人
				->setTitle("Office 2007 XLSX Test Document")//标题
				->setSubject("Office 2007 XLSX Test Document")//题目
				->setDescription("Test document for Office 2007 XLSX.")//描述
				->setKeywords("office 2007 openxml php")//关键字
				->setCategory("Test result file"); //种类
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		// Rename worksheet 设置sheet的name
		$objPHPExcel->getActiveSheet()->setTitle('One Sheet');

		// Add some data 设置单元格的值 方式1
		$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'Hello')
				->setCellValue('A2', 'world!')
				->setCellValue('A3', 'world!')
				->setCellValue('A4', 'world!')
				->setCellValue('B1', '你好')
				->setCellValue('B2', '你好');
		// Add some data 设置单元格的值 方式2
		$objPHPExcel->getActiveSheet()
				->setCellValue('C1', '120.3325')
				->setCellValue('C2', '523.2')
				->setCellValue('C3', '33')
				->setCellValue('C4', '0.252')
				->setCellValue('C5', '0.252')
				->setCellValue('C6', '0.252');

		// 合并单元格
		$objPHPExcel->getActiveSheet()->mergeCells('A18:E22');

		// 设置单元格font
		$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setName('Candara');
		$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setSize(20);
		$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		// 设置单元格格式
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
		// 格式刷 其他单元区域
		$objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle('C1'), 'C2:C4');
		// 设置单元格 水平 align
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		// 设置单元格 垂直 align
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		// 设置单元格 边框
		$objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		// 设置单元格 边框颜色
		$objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getLeft()->getColor()->setARGB('FF0000FF');
		$objPHPExcel->getActiveSheet()->getStyle('B1')->getBorders()->getTop()->getColor()->setARGB('FF0000FF');
		// 设置单元格 背景色
		$objPHPExcel->getActiveSheet()->getStyle('C4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('C4')->getFill()->getStartColor()->setARGB('FF00FF00');
		// 设置宽单元格width
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);

		// 创建一个新的 sheet
		$objPHPExcel->createSheet();
		// 设置第二个 sheet 为当前，新增sheet时，数字递增
		$objPHPExcel->setactivesheetindex(1);
		// Rename worksheet 设置sheet的name
		$objPHPExcel->getActiveSheet()->setTitle('Sec Sheet');
		// 为新 sheet 赋值
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'New');
		// 保护工作表 Needs to be set to true in order to enable any worksheet protection! 
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		// 设置允许用户编辑区域
		$objPHPExcel->getActiveSheet()->protectCells('A3:E13', 'PHPExcel');


		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01simple.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: 0'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}

}
