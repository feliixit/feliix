<?php
require 'vendor/autoload.php'; // Include Composer's autoloader
// composer require picqer/php-barcode-generator
use Picqer\Barcode\BarcodeGeneratorPNG;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$barcode = null; // Initialize variable for barcode image

$items = $_POST['items'];

$items = json_decode($items, true);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <style>
        body {
            margin: 0;
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        div.label_block {
            width: 43.5mm;
            height: 23mm;
            border: 1px solid black;
            display: inline-block;
            margin-top: 0.5mm;
            margin-left: 1.7mm;
        }

        div.label_block.right {
            margin-left: 2mm;
        }

        div.label_block table {
            width: 100%;
        }

        div.label_block table td {
            font-size: 10px;
        }

        div.label_block table tr:nth-of-type(1) td:nth-of-type(1) {
            padding: 0 1mm 0 1.5mm;
        }

        div.label_block table tr:nth-of-type(1) td:nth-of-type(1) div.id {
            margin-top: 1mm;
        }

        div.label_block table tr:nth-of-type(1) td:nth-of-type(1) div.code {
            word-break: break-all;
        }

        div.label_block table tr:nth-of-type(1) td:nth-of-type(2) {
            width: 20%;
            padding: 1mm 1mm 0 1mm;
            vertical-align: top;
        }

        div.label_block table tr:nth-of-type(1) td:nth-of-type(2) img {
            width: 12mm;
        }

        div.label_block table tr:nth-of-type(2) td:nth-of-type(1) {
            padding: 0 1mm;
            text-align: center;
        }

        div.label_block table tr:nth-of-type(2) td:nth-of-type(1) img {
            width: 37mm;
        }

        @media print {
            div.label_block {
                border: none;
            }
        }
        
        @page {
            size: 92mm 25mm;
            size: landscape;
            margin: 0;
        }
    </style>
</head>
<body>

<?php
    $even = 0;
    foreach ($items as $item) {
        $even++;
        $pid = $item['p'];
        $code = $item['c'];
        $bar = $item['b'];
    
        $barcodeInput = $bar;

        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($barcodeInput, $generator::TYPE_CODE_128);

        $qrcodeInput = urldecode("https://feliix.myvnc.com/tracking_item_query?tid=" . $bar); // Escape user input
        //$encodedInput = urlencode($qrcodeInput); // URL encode the input
        $qrCode = new QrCode($qrcodeInput);
        $qrCode->setSize(300);

        // Use PngWriter to generate the QR code image
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrCodeImage = $result->getDataUri(); // Get the data URI for the QR code image
        $format_bar = substr($bar, 0, 6) . ' ' . substr($bar, 6, 5) . ' ' . substr($bar, 11);

?>
    <div class="label_block<?php echo ($even % 2 == 0) ? ' right' : ''; ?>">
        <table>
            <tbody>
            <tr>
                <td>
                    <div class="id">ID: <?= $pid ?></div>

                    <div class="code"><?= $code ?></div>
                </td>

                <td>
                    <img src="<?php echo $qrCodeImage; ?>" alt="QR Code">
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <img src="data:image/png;base64,<?= base64_encode($barcode) ?>" alt="Barcode">
                    <br><strong><?php echo $format_bar; ?></strong>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php
    }
?>


</body>
</html>
