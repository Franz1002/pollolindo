<?php
// ============================================================
// SCRIPT DE UN SOLO USO - Migra contraseñas de texto plano a hash
// 1. Copia este archivo a la raíz de tu proyecto (junto a index.php)
// 2. Ábrelo una vez desde el navegador: http://localhost/pollo/migrar_passwords.php
// 3. Verifica que diga "Listo" para cada usuario
// 4. BORRA este archivo del servidor inmediatamente después de usarlo
// ============================================================

require_once 'Config/Config.php';
require_once 'Libraries/Core/Conexion.php';

$conexion = new Conexion();
$pdo = $conexion->conect();

$stmt = $pdo->prepare("SELECT id, cedula, password FROM usuario");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($usuarios as $usuario) {
    // Si ya empieza con $2y$ ya está hasheada, se salta (evita doble-hash si corres esto 2 veces)
    if (strpos($usuario['password'], '$2y$') === 0) {
        echo "Usuario {$usuario['cedula']}: ya estaba hasheada, se omite.<br>";
        continue;
    }

    $hash = password_hash($usuario['password'], PASSWORD_BCRYPT);

    $update = $pdo->prepare("UPDATE usuario SET password = ? WHERE id = ?");
    $update->execute([$hash, $usuario['id']]);

    echo "Usuario {$usuario['cedula']}: Listo.<br>";
}

echo "<br><strong>Migración completa. BORRA este archivo ahora.</strong>";
