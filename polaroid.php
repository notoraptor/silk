<?php
/**
 * Created by PhpStorm.
 * User: notoraptor
 * Date: 11/09/2018
 * Time: 02:15
 */
require_once('server_infos.php');
require_once('priv/utils.php');
// require_once ('script/fpdf181/fpdf.php');
require('script/tfpdf/tfpdf.php');
$id = utils_s_get('id');
$origin = utils_s_get('origin');
if (!ctype_digit($id) || !file_exists($origin.'.php'))
	utils_redirection('index.php');
$db = new Database();
$model = $db->model($id);
if(!$model) utils_redirection('index.php');

$unicodeChar = '\u1000';
$decodedChar = json_decode('"'.$unicodeChar.'"');

$detail_names = array('height', 'bust', 'waist', 'hips', 'shoes', 'hair', 'eyes');
$detail_suffixes = array(' cm', ' cm', ' cm', ' cm', '', '', '');
$detail_titles = ARRAY('Height', 'Bust', 'Waist', 'Hips', 'Shoes', 'Hair', 'Eyes');
$details = '';
for ($i = 0; $i < count($detail_names); ++$i) {
	$detail_name = $detail_names[$i];
	$detail_title = $detail_titles[$i];
	$detail_suffix = $detail_suffixes[$i];
	if ($i) {
		$details .= '•';
	}
	$details .= $detail_title.': '.$model->$detail_name.$detail_suffix;
}

$photoPaths = array();
$photoLinks = array();
$photoDimensions = array();
foreach(array('photo_1', 'photo_2', 'photo_3', 'photo_4') as $photoID) {
	if ($model->$photoID) {
		$photoInfo = $model->getPhotoByBasename($model->$photoID);
		$photoPaths[] = $photoInfo['path'];
		$photoLinks[] = $photoInfo['url'];
		$imageInfo = getimagesize($photoInfo['path']);
		$photoDimensions[] = array($imageInfo[0], $imageInfo[1]);
	}
}
$printRatio = 1.375/2;
$printWidthInches = 2.4;
$printHeightInches = $printWidthInches / $printRatio;
$printPaddingInches = 1.75;
$printHSpaceInches = 8.5 - 2 * $printPaddingInches - 2 * $printWidthInches;
$printVSpaceInches = $printHSpaceInches / 2;
$coordinates = array(
	$printPaddingInches,
	$printPaddingInches + $printWidthInches + $printHSpaceInches
);

$pdf = new tFPDF('P', 'in', 'Letter');
$pdf->SetMargins(0, 0.7, 0);
$pdf->AddPage();
$pdf->AddFont('BEBAS', 'B', 'BEBAS.php');
$pdf->AddFont('san','','SourceHanSerifCN-Regular.ttf',true);
$pdf->AddFont('sanb','','SourceHanSerifCN-Bold.ttf',true);

$pdf->SetFont('BEBAS', 'B', 118);
$pdf->Cell(0, 1, 'SILK', 0, 1, 'C');

$pdf->SetFont('Arial','',20);
$pdf->Cell(0, 0.52, 'M A N A G E M E N T', 0, 1, 'C');

$pdf->SetFillColor(204, 204, 204);
for ($i = 0; $i < count($photoLinks); ++$i) {
	if ($i == 2)
		$pdf->Ln($printHeightInches + $printVSpaceInches);
	$rectX = $coordinates[$i % 2];
	$rectY = $pdf->GetY();
	$photoPath = $photoPaths[$i];
	$dimensions = $photoDimensions[$i];
	$width = $dimensions[0];
	$height = $dimensions[1];
	$imagePrintWidth = 0;
	$imagePrintHeight = 0;
	$imagePrintX = 0;
	$imagePrintY = 0;
	if ($width / $height == $printRatio) {
		$imagePrintWidth = $printWidthInches;
		$imagePrintHeight = $printHeightInches;
		$imagePrintX = $rectX;
		$imagePrintY = $rectY;
	} else if ($width >= $height) {
		$imagePrintWidth = $printWidthInches;
		$imagePrintHeight = 0;
		$imagePrintX = $rectX;
		$imagePrintY = $rectY + ($printHeightInches - ($printWidthInches * $height / $width)) / 2;
	} else {
		$imagePrintWidth = 0;
		$imagePrintHeight = $printHeightInches;
		$imagePrintX = $rectX + ($printWidthInches - ($printHeightInches * $width / $height)) / 2;
		$imagePrintY = $rectY;
	}
	$pdf->Rect($rectX, $rectY, $printWidthInches, $printHeightInches, 'F');
	$pdf->Image($photoPath, $imagePrintX, $imagePrintY, $imagePrintWidth, $imagePrintHeight);
}
$pdf->Ln($printHeightInches + $printVSpaceInches);

$pdf->SetFont('sanb','',11);
$pdf->Cell(0, 0.18, strtoupper($model->first_name.' '.$model->last_name), 0, 1, 'C');

$pdf->SetFont('san','',9);
$pdf->Cell(0, 0.40, $details, 0, 1, 'C');
if ($model->address) {
	$pdf->Cell(0, 0.12, $model->address, 0, 1, 'C');
}

$pdf->Output();
?>