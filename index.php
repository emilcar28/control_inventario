<?php include 'header.php';
require_once 'vendor/autoload.php';
 ?>





<h2>Bienvenido, <?php echo $_SESSION['usuario']; ?>!</h2>
<p>Utiliza el menú para navegar por el sistema de inventario.</p>

<?php include 'footer.php'; ?>
