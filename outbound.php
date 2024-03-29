<?php
session_start();

$con = mysqli_connect('localhost', 'root', '', 'experiment');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['save_excel_data'])) {
    $fileName = $_FILES['import_file']['name'];
    $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);

    $allowed_ext = ['csv', 'xls', 'xlsx'];

    if (in_array($file_ext, $allowed_ext)) {
        $inputFileNamePath = $_FILES['import_file']['tmp_name'];

        // Load the file into a Spreadsheet object
        $spreadsheet = IOFactory::load($inputFileNamePath);
        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Skip the header row
        $headerSkipped = false;

        foreach ($data as $row) {
            // Skip the header row
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            // Retrieve data from the row
            $SN = isset($row['A']) ? $row['A'] : '';
            $item_id = isset($row['B']) ? $row['B'] : '';
            $item_description = isset($row['C']) ? $row['C'] : '';
            $item_quantity = isset($row['D']) ? $row['D'] : '';
            $unit_price = isset($row['E']) ? $row['E'] : '';
            $date_shipped = isset($row['F']) ? $row['F'] : '';
            $department = isset($row['G']) ? $row['G'] : '';
            $destination = isset($row['H']) ? $row['H'] : '';
            $total_price = isset($row['I']) ? $row['I'] : '';
            $remarks = isset($row['J']) ? $row['J'] : '';

            // SQL query to insert data
            $outboundQuery = "INSERT INTO outbound (SN,item_id, item_description, item_quantity, unit_price, date_shipped, department, destination, total_price, remarks) VALUES 
            ('$SN',
            '$item_id',
            '$item_description',
            '$item_quantity',
            '$unit_price',
            '$date_shipped',
            '$department',
            '$destination',
            '$total_price',
            '$remarks')";

            // Execute the query
            $result = mysqli_query($con, $outboundQuery);
            if ($result) {
                $msg = "Successfully imported";
            } else {
                $msg = "Error occurred while importing data: " . mysqli_error($con);
                break; // Exit the loop if an error occurs
            }
        }

        $_SESSION['message'] = $msg;
        header('location: outbound.php');
        exit(0);
    } else {
        $_SESSION['message'] = "Invalid file format. Please upload a CSV, XLS, or XLSX file.";
        header('location: outbound.php');
        exit(0);
    }
}
// Function to download the Excel file
function downloadExcelFile($con) {
    require_once 'vendor/autoload.php'; // Include autoload.php

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add header row
    $headerRow = ['SN', 'Item Id', 'Item Description', 'Item Quantity', 'Unit Price', 'Date Shipped', 'Department','Destination', 'Total Price', 'Remarks'];
    $column = 'A';
    foreach ($headerRow as $headerCell) {
        $sheet->setCellValue($column++ . '1', $headerCell);
    }

    // Fetch data from the database
    $sql = "SELECT * FROM outbound";
    $result = mysqli_query($con, $sql);
    $rowCount = 2; // Start from the second row after the header

    // Add data rows
    while ($row = mysqli_fetch_assoc($result)) {
        $column = 'A';
        foreach ($row as $cell) {
            $sheet->setCellValue($column++ . $rowCount, $cell);
        }
        $rowCount++;
    }

    // Set headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="outbound_data.xlsx"');
    header('Cache-Control: max-age=0');

    // Write to output
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
}

if(isset($_POST['download_excel'])) {
    downloadExcelFile($con);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <title>Search Outbound Data</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container-box {
            border: 2px solid #007bff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            background-color: #fff;
        }

        .form-control-sm {
            border-radius: 5px;
        }

        .btn-dark {
            border-radius: 5px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .btn-primary {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="container-box">
                    <form class="my-2 mx-2" method="post">
                        <h2 class="mb-4">Search Outbound Details</h2>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm mb-3" name="item_id" placeholder="Item ID">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm mb-3" name="item_description" placeholder="Item description">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm mb-3" name="date_shipped" placeholder="Extract Date">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm mb-3" name="Department" placeholder="Department name">
                            </div>
                        </div>
                        <button type="submit" name="submit" class="btn btn-dark">Search</button>
                    </form>
                    <div class="container my-2 mx-2 table-container">
                        <table class="table table-bordered border-primary">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Item Id</th>
                                    <th>Item Description</th>
                                    <th>Item Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Date Shipped</th>
                                    <th>Departemnt</th>
                                    <th>Destination</th>
                                    <th>Total Price</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!isset($_POST['submit'])) {
                                    $sql = "SELECT * FROM `outbound` WHERE SN <= 50"; // Limit data to SN 50
                                    $result = mysqli_query($con, $sql);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>
                                                <td>' . $row['SN'] . '</td>
                                                <td>' . $row['item_id'] . '</td>
                                                <td>' . $row['item_description'] . '</td>
                                                <td>' . $row['item_quantity'] . '</td>
                                                <td>' . $row['unit_price'] . '</td>
                                                <td>' . $row['date_shipped'] . '</td>
                                                <td>' . $row['department'] . '</td>
                                                <td>' . $row['destination'] . '</td>
                                                <td>' . $row['total_price'] . '</td>
                                                <td>' . $row['remarks'] . '</td>
                                              </tr>';
                                    }
                                } else {
                                    $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : '';
                                    $item_description = isset($_POST['item_description']) ? $_POST['item_description'] : '';
                                    $date_shipped = isset($_POST['date_shipped']) ? $_POST['date_shipped'] : '';
                                    $department = isset($_POST['department']) ? $_POST['department'] : '';

                                    $sql = "SELECT * FROM `outbound` WHERE 1=1";

                                    if (!empty($item_id)) {
                                        $sql .= " AND item_id LIKE '%$item_id%'";
                                    }
                                    if (!empty($item_description)) {
                                        $sql .= " AND item_description LIKE '%$item_description%'";
                                    }
                                    if (!empty($date_shipped)) {
                                        $sql .= " AND date_shipped LIKE '%$date_shipped%'";
                                    }
                                    if (!empty($department)) {
                                        $sql .= " AND department LIKE '%$department%'";
                                    }

                                    $result = mysqli_query($con, $sql);
                                    if ($result) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            if ($row['SN'] <= 50) { // Only display rows with SN up to 80
                                                echo '<tr>
                                                    <td>' . $row['SN'] . '</td>
                                                    <td>' . $row['item_id'] . '</td>
                                                    <td>' . $row['item_description'] . '</td>
                                                    <td>' . $row['item_quantity'] . '</td>
                                                    <td>' . $row['unit_price'] . '</td>
                                                    <td>' . $row['date_shipped'] . '</td>
                                                    <td>' . $row['department'] . '</td>
                                                    <td>' . $row['destination'] . '</td>
                                                    <td>' . $row['total_price'] . '</td>
                                                    <td>' . $row['remarks'] . '</td>
                                                  </tr>';
                                            }
                                        }
                                    } else {
                                        echo '<tr><td colspan="8">Data not found</td></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="file" name="import_file" class="form-control" />
                        <button type="submit" name="save_excel_data" class="btn btn-primary mt-3">Import</button>
                    </form>
                    <form method="post" action="">
                        <button type="submit" name="reset" class="btn btn-danger mt-3">Reset</button>
                    </form>
                    <form method="POST" action="">
                        <button type="submit" name="download_excel" class="btn btn-success mt-3">Download Excel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>