<?php
// Incluye la biblioteca PHPExcel
require 'PHPExcel/PHPExcel.php';

// Ruta al archivo Excel donde se guardarán las reservas
$archivo_excel = 'reservas.xlsx';

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $torre = $_POST["torre"];
    $apartamento = $_POST["apartamento"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];

    // Verificar si ya hay una reserva para el día seleccionado
    $reservas_mismo_dia = 0;

    // Crea un objeto PHPExcel
    $objPHPExcel = new PHPExcel();

    // Verifica si el archivo de Excel ya existe
    if (file_exists($archivo_excel)) {
        // Carga el archivo Excel existente
        $objPHPExcel = PHPExcel_IOFactory::load($archivo_excel);

        // Obtiene la hoja activa
        $hoja = $objPHPExcel->getActiveSheet();

        // Recorre las filas del archivo Excel para verificar si ya existe una reserva para el día seleccionado
        foreach ($hoja->getRowIterator() as $fila) {
            $datos_fila = $fila->getRowIndex();
            if ($hoja->getCell('E' . $datos_fila)->getValue() == $fecha) {
                $reservas_mismo_dia++;
            }
        }
    }

    // Si hay reservas para el mismo día, mostrar mensaje
    if ($reservas_mismo_dia > 0) {
        echo "<script>alert('Ya hay $reservas_mismo_dia reserva(s) para el día seleccionado');</script>";
    } else {
        // Si no hay reservas para el mismo día, guardar la nueva reserva
        $objPHPExcel->setActiveSheetIndex(0);
        $hoja = $objPHPExcel->getActiveSheet();

        // Encabezados
        $hoja->setCellValue('A1', 'Nombre');
        $hoja->setCellValue('B1', 'Email');
        $hoja->setCellValue('C1', 'Torre');
        $hoja->setCellValue('D1', 'Apartamento');
        $hoja->setCellValue('E1', 'Fecha');
        $hoja->setCellValue('F1', 'Hora');

        // Siguiente fila disponible
        $fila = $hoja->getHighestRow() + 1;

        // Guarda los datos de la reserva en el archivo Excel
        $hoja->setCellValue('A' . $fila, $nombre);
        $hoja->setCellValue('B' . $fila, $email);
        $hoja->setCellValue('C' . $fila, $torre);
        $hoja->setCellValue('D' . $fila, $apartamento);
        $hoja->setCellValue('E' . $fila, $fecha);
        $hoja->setCellValue('F' . $fila, $hora);

        // Guarda el archivo Excel
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($archivo_excel);

        // Mostrar mensaje de confirmación
        echo "<script>alert('Registro exitoso');</script>";
    }
}

// Redireccionar de vuelta a la página anterior
echo "<script>window.history.back();</script>";
?>