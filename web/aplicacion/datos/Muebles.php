<?php
// Array indexado con posiciones a partir de 1
$muebles = [
    1 => new MuebleReciclado('Silla', 'FaMu:Ikea', 'ESPAÑA', 2021, '01/03/2021', '31/12/2030', 45, 60),
    2 => new MuebleTradicional('Mesa', 'FaMu:Roble', 'ESPAÑA', 2022, '01/02/2022', '31/12/2035', 120, 40, 'S02'),
    3 => new MuebleReciclado('Estantería', 'FaMu:Eco', 'PORTUGAL', 2020, '01/01/2020', '31/12/2040', 80, 30),
    4 => new MuebleTradicional('Armario', 'FaMu:Nogal', 'ESPAÑA', 2023, '01/06/2023', '31/12/2040', 300, 150, 'S03'),
    5 => new MuebleReciclado('Banco', 'FaMu:Verde', 'ESPAÑA', 2024, '01/01/2024', '31/12/2040', 60, 90),
];

// Al menos dos características adicionales por mueble
$muebles[1]->anadirCaracteristicas('color', 'blanco', 'apilable', true);
$muebles[2]->anadirCaracteristicas('material', 'roble', 'extensible', false, 'patas', 4);
$muebles[3]->anadirCaracteristicas('baldas', 5, 'material', 'pino reciclado');
$muebles[4]->anadirCaracteristicas('puertas', 3, 'espejo', true);
$muebles[5]->anadirCaracteristicas('exterior', true, 'color', 'verde');
