<?php
require_once __DIR__ . "/Calculadora.php";

$resultado = null;
$error = "";
$exp = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $exp = $_POST["expresion"] ?? "";
    $calc = new Calculadora();
    try {
        $resultado = $calc->evaluar($exp);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Calculadora de Expresiones (PHP puro)</title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <h2>Calculadora de Expresiones en PHP</h2>
  <form method="post">
    <label>Ingrese expresión (ej: 2+3*4 o (2+3)*4):</label><br>
    <input type="text" name="expresion" value="<?= htmlspecialchars($exp) ?>" required>
    <button type="submit">Calcular</button>
  </form>

  <?php if ($resultado !== null && $error === ""): ?>
    <p class="ok"><b>Resultado:</b> <?= htmlspecialchars((string)$resultado) ?></p>
  <?php endif; ?>

  <?php if ($error !== ""): ?>
    <p class="err"><b>Error:</b> <?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
</body>
</html>
